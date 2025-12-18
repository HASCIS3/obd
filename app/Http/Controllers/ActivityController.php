<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityMedia;
use App\Models\Discipline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $now = now();
        $type = $request->get('type');

        $query = Activity::query()->where('publie', true)->with('discipline');

        if ($type && array_key_exists($type, Activity::TYPES)) {
            $query->where('type', $type);
        }

        $aVenir = (clone $query)
            ->where('debut', '>=', $now)
            ->orderBy('debut')
            ->paginate(10, ['*'], 'a_venir')
            ->withQueryString();

        $precedentes = (clone $query)
            ->where('debut', '<', $now)
            ->orderBy('debut', 'desc')
            ->paginate(10, ['*'], 'precedentes')
            ->withQueryString();

        $types = Activity::TYPES;

        return view('activities.index', compact('aVenir', 'precedentes', 'types', 'type'));
    }

    public function show(Activity $activity): View
    {
        abort_unless($activity->publie || auth()->user()?->isAdmin(), 404);

        $activity->load(['discipline', 'medias', 'createur']);

        return view('activities.show', compact('activity'));
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
