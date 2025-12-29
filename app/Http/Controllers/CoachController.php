<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CoachController extends Controller
{
    /**
     * Affiche la liste des coachs
     */
    public function index(Request $request): View
    {
        $query = Coach::with(['user', 'disciplines']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('discipline')) {
            $query->whereHas('disciplines', function ($q) use ($request) {
                $q->where('disciplines.id', $request->discipline);
            });
        }

        if ($request->filled('actif')) {
            $query->where('actif', $request->actif === '1');
        }

        $coachs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $disciplines = Discipline::where('actif', true)->orderBy('nom')->get();

        return view('coachs.index', compact('coachs', 'disciplines'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create(): View
    {
        // Charger toutes les disciplines pour permettre de sélectionner
        $disciplines = Discipline::orderBy('nom')->get();
        return view('coachs.create', compact('disciplines'));
    }

    /**
     * Enregistre un nouveau coach
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'specialite' => 'nullable|string|max:255',
            'date_embauche' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
            'disciplines' => 'nullable|array',
            'disciplines.*' => 'exists:disciplines,id',
        ]);

        // Gestion de la photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coachs', 'public');
        }

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_COACH,
            'email_verified_at' => now(),
        ]);

        // Créer le profil coach
        $coach = Coach::create([
            'user_id' => $user->id,
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'specialite' => $validated['specialite'] ?? null,
            'photo' => $photoPath,
            'date_embauche' => $validated['date_embauche'] ?? now(),
            'actif' => true,
        ]);

        // Attacher les disciplines
        if (!empty($validated['disciplines'])) {
            $coach->disciplines()->attach($validated['disciplines']);
        }

        return redirect()->route('coachs.show', $coach)
            ->with('success', 'Coach créé avec succès.');
    }

    /**
     * Affiche les détails d'un coach
     */
    public function show(Coach $coach): View
    {
        $coach->load(['user', 'disciplines', 'presences' => fn($q) => $q->orderBy('date', 'desc')->take(20)]);

        return view('coachs.show', compact('coach'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Coach $coach): View
    {
        // Charger toutes les disciplines pour permettre de voir/modifier les associations existantes
        $disciplines = Discipline::orderBy('nom')->get();
        $coach->load(['user', 'disciplines']);

        return view('coachs.edit', compact('coach', 'disciplines'));
    }

    /**
     * Met à jour un coach
     */
    public function update(Request $request, Coach $coach): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $coach->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'specialite' => 'nullable|string|max:255',
            'date_embauche' => 'nullable|date',
            'actif' => 'boolean',
            'photo' => 'nullable|image|max:2048',
            'disciplines' => 'nullable|array',
            'disciplines.*' => 'exists:disciplines,id',
        ]);

        // Gestion de la photo
        $photoPath = $coach->photo;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coachs', 'public');
        }

        // Mettre à jour l'utilisateur
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $coach->user->update($userData);

        // Mettre à jour le profil coach
        $coach->update([
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
            'specialite' => $validated['specialite'] ?? null,
            'photo' => $photoPath,
            'date_embauche' => $validated['date_embauche'],
            'actif' => $request->has('actif'),
        ]);

        // Synchroniser les disciplines
        if (isset($validated['disciplines'])) {
            $coach->disciplines()->sync($validated['disciplines']);
        }

        return redirect()->route('coachs.show', $coach)
            ->with('success', 'Coach mis à jour avec succès.');
    }

    /**
     * Supprime un coach
     */
    public function destroy(Coach $coach): RedirectResponse
    {
        $user = $coach->user;
        $coach->delete();
        $user->delete();

        return redirect()->route('coachs.index')
            ->with('success', 'Coach supprimé avec succès.');
    }
}
