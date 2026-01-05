<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityMedia;
use App\Models\Discipline;
use App\Models\Rencontre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $now = now();
        $type = $request->get('type');

        $query = Activity::query()->where('publie', true)->with('discipline');

        // Filtrer par type si spécifié (sauf 'match' qui vient des rencontres)
        if ($type && $type !== 'match' && array_key_exists($type, Activity::TYPES)) {
            $query->where('type', $type);
        }

        // Réponse API JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $activities = (clone $query)->orderByDesc('debut')->get()->map(function ($a) {
                return [
                    'id' => $a->id,
                    'titre' => $a->titre,
                    'description' => $a->description,
                    'type' => $a->type,
                    'debut' => $a->debut,
                    'fin' => $a->fin,
                    'lieu' => $a->lieu,
                    'image' => $a->image ? asset('storage/' . $a->image) : null,
                    'discipline_id' => $a->discipline_id,
                    'discipline_nom' => $a->discipline?->nom,
                ];
            });
            return response()->json(['data' => $activities]);
        }

        // Activités à venir
        $aVenir = (clone $query)
            ->where('debut', '>=', $now)
            ->orderBy('debut')
            ->paginate(10, ['*'], 'a_venir')
            ->withQueryString();

        // Activités précédentes
        $activitesPrecedentes = (clone $query)
            ->where('debut', '<', $now)
            ->orderBy('debut', 'desc')
            ->get();

        // Rencontres passées (matchs joués)
        $rencontresPrecedentes = collect();
        if (!$type || $type === 'match') {
            $rencontresPrecedentes = Rencontre::with('discipline')
                ->where('date_match', '<', $now->toDateString())
                ->orderBy('date_match', 'desc')
                ->get()
                ->map(function ($r) {
                    return (object) [
                        'id' => $r->id,
                        'type' => 'match',
                        'titre' => 'Match vs ' . $r->adversaire,
                        'description' => $r->remarques,
                        'lieu' => $r->lieu,
                        'debut' => $r->date_match,
                        'discipline' => $r->discipline,
                        'resultat' => $r->resultat,
                        'score_obd' => $r->score_obd,
                        'score_adversaire' => $r->score_adversaire,
                        'is_rencontre' => true,
                    ];
                });
        }

        // Fusionner et trier par date décroissante
        $precedentesCollection = $activitesPrecedentes->merge($rencontresPrecedentes)
            ->sortByDesc('debut')
            ->values();

        // Paginer manuellement
        $page = $request->get('precedentes', 1);
        $perPage = 10;
        $precedentes = new \Illuminate\Pagination\LengthAwarePaginator(
            $precedentesCollection->forPage($page, $perPage),
            $precedentesCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'precedentes', 'query' => $request->query()]
        );

        $types = Activity::TYPES;

        return view('activities.index', compact('aVenir', 'precedentes', 'types', 'type'));
    }

    public function show(Request $request, Activity $activity)
    {
        abort_unless($activity->publie || auth()->user()?->isAdmin(), 404);

        $activity->load(['discipline', 'medias', 'createur']);

        // Réponse API JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => [
                    'id' => $activity->id,
                    'titre' => $activity->titre,
                    'description' => $activity->description,
                    'type' => $activity->type,
                    'debut' => $activity->debut,
                    'fin' => $activity->fin,
                    'lieu' => $activity->lieu,
                    'image' => $activity->image ? asset('storage/' . $activity->image) : null,
                    'discipline_id' => $activity->discipline_id,
                    'discipline_nom' => $activity->discipline?->nom,
                ]
            ]);
        }

        return view('activities.show', compact('activity'));
    }

    /**
     * Activités à venir (API)
     */
    public function aVenir()
    {
        $activities = Activity::with('discipline')
            ->where('publie', true)
            ->where('debut', '>=', now())
            ->orderBy('debut')
            ->limit(10)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'titre' => $a->titre,
                    'description' => $a->description,
                    'type' => $a->type,
                    'debut' => $a->debut,
                    'fin' => $a->fin,
                    'lieu' => $a->lieu,
                    'image' => $a->image ? asset('storage/' . $a->image) : null,
                    'discipline_id' => $a->discipline_id,
                    'discipline_nom' => $a->discipline?->nom,
                ];
            });

        return response()->json(['data' => $activities]);
    }

    public function create(): View
    {
        $types = Activity::TYPES;
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();

        return view('activities.create', compact('types', 'disciplines'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Activity::TYPES)),
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lieu' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url|max:500',
            'discipline_id' => 'nullable|exists:disciplines,id',
            'debut' => 'required|date',
            'fin' => 'nullable|date|after_or_equal:debut',
            'publie' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('activities', 'public');
        }

        $validated['publie'] = $request->boolean('publie', true);
        $validated['created_by'] = auth()->id();

        $activity = Activity::create($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activite creee avec succes.');
    }

    public function edit(Activity $activity): View
    {
        $types = Activity::TYPES;
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();
        $activity->load('medias');

        return view('activities.edit', compact('activity', 'types', 'disciplines'));
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Activity::TYPES)),
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lieu' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url|max:500',
            'discipline_id' => 'nullable|exists:disciplines,id',
            'debut' => 'required|date',
            'fin' => 'nullable|date|after_or_equal:debut',
            'publie' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('activities', 'public');
        }

        $validated['publie'] = $request->boolean('publie', true);

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activite mise a jour avec succes.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activite supprimee avec succes.');
    }

    public function addMedia(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:photo,video',
            'media' => 'required_if:type,photo|image|max:5120',
            'url' => 'required_if:type,video|nullable|url|max:500',
            'titre' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $mediaData = [
            'activity_id' => $activity->id,
            'type' => $validated['type'],
            'titre' => $validated['titre'] ?? null,
            'description' => $validated['description'] ?? null,
            'ordre' => $activity->medias()->count(),
        ];

        if ($validated['type'] === 'photo' && $request->hasFile('media')) {
            $mediaData['url'] = $request->file('media')->store('activities/medias', 'public');
        } else {
            $mediaData['url'] = $validated['url'];
        }

        ActivityMedia::create($mediaData);

        return redirect()->route('activities.edit', $activity)
            ->with('success', 'Media ajoute avec succes.');
    }

    public function deleteMedia(Activity $activity, ActivityMedia $media): RedirectResponse
    {
        abort_unless($media->activity_id === $activity->id, 404);

        $media->delete();

        return redirect()->route('activities.edit', $activity)
            ->with('success', 'Media supprime avec succes.');
    }
}
