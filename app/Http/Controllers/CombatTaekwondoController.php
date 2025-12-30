<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\CombatTaekwondo;
use App\Models\Rencontre;
use Illuminate\Http\Request;

class CombatTaekwondoController extends Controller
{
    public function index(Rencontre $rencontre)
    {
        $combats = $rencontre->combatsTaekwondo()->with(['athleteRouge', 'athleteBleu'])->get();
        
        return view('combats-taekwondo.index', compact('rencontre', 'combats'));
    }

    public function create(Rencontre $rencontre)
    {
        $athletes = Athlete::whereHas('disciplines', function ($q) use ($rencontre) {
            $q->where('disciplines.id', $rencontre->discipline_id);
        })->where('actif', true)->orderBy('nom')->get();

        return view('combats-taekwondo.create', compact('rencontre', 'athletes'));
    }

    public function store(Request $request, Rencontre $rencontre)
    {
        $validated = $request->validate([
            'athlete_rouge_id' => 'nullable|exists:athletes,id',
            'nom_rouge' => 'required_without:athlete_rouge_id|string|max:100',
            'club_rouge' => 'nullable|string|max:100',
            'categorie_rouge' => 'nullable|string|max:50',
            'athlete_bleu_id' => 'nullable|exists:athletes,id',
            'nom_bleu' => 'required_without:athlete_bleu_id|string|max:100',
            'club_bleu' => 'nullable|string|max:100',
            'categorie_bleu' => 'nullable|string|max:50',
            'categorie_poids' => 'nullable|string|max:50',
            'categorie_age' => 'nullable|string|max:50',
        ]);

        $combat = CombatTaekwondo::create([
            'rencontre_id' => $rencontre->id,
            'athlete_rouge_id' => $validated['athlete_rouge_id'] ?? null,
            'nom_rouge' => $validated['nom_rouge'] ?? null,
            'club_rouge' => $validated['club_rouge'] ?? null,
            'categorie_rouge' => $validated['categorie_rouge'] ?? null,
            'athlete_bleu_id' => $validated['athlete_bleu_id'] ?? null,
            'nom_bleu' => $validated['nom_bleu'] ?? null,
            'club_bleu' => $validated['club_bleu'] ?? null,
            'categorie_bleu' => $validated['categorie_bleu'] ?? null,
            'categorie_poids' => $validated['categorie_poids'] ?? null,
            'categorie_age' => $validated['categorie_age'] ?? null,
            'rounds' => CombatTaekwondo::getDefaultRounds(),
            'statut' => 'a_jouer',
        ]);

        return redirect()->route('combats-taekwondo.saisie', [$rencontre, $combat])
            ->with('success', 'Combat créé avec succès.');
    }

    public function saisie(Rencontre $rencontre, CombatTaekwondo $combat)
    {
        return view('combats-taekwondo.saisie', compact('rencontre', 'combat'));
    }

    public function updateScores(Request $request, Rencontre $rencontre, CombatTaekwondo $combat)
    {
        $validated = $request->validate([
            'rounds' => 'required|array',
            'round_actuel' => 'nullable|integer|min:1|max:4',
            'statut' => 'nullable|in:a_jouer,en_cours,termine',
        ]);

        $combat->rounds = $validated['rounds'];
        
        if (isset($validated['round_actuel'])) {
            $combat->round_actuel = $validated['round_actuel'];
        }
        
        if (isset($validated['statut'])) {
            $combat->statut = $validated['statut'];
        }

        $combat->calculerScores();

        // Vérifier victoire automatique
        $typeVictoire = $combat->verifierVictoireAutomatique();
        if ($typeVictoire && $combat->statut === 'en_cours') {
            $combat->statut = 'termine';
            $combat->type_victoire = $typeVictoire;
            $combat->vainqueur = $combat->score_rouge > $combat->score_bleu ? 'rouge' : 'bleu';
        }

        $combat->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'score_rouge' => $combat->score_rouge,
                'score_bleu' => $combat->score_bleu,
                'statut' => $combat->statut,
                'vainqueur' => $combat->vainqueur,
                'type_victoire' => $combat->type_victoire,
            ]);
        }

        return back()->with('success', 'Scores mis à jour.');
    }

    public function terminer(Request $request, Rencontre $rencontre, CombatTaekwondo $combat)
    {
        $validated = $request->validate([
            'vainqueur' => 'required|in:rouge,bleu,nul',
            'type_victoire' => 'required|in:points,ecart_20,disqualification,abandon,ko,decision_arbitre',
            'remarques' => 'nullable|string',
        ]);

        $combat->update([
            'statut' => 'termine',
            'vainqueur' => $validated['vainqueur'],
            'type_victoire' => $validated['type_victoire'],
            'remarques' => $validated['remarques'] ?? null,
        ]);

        return redirect()->route('combats-taekwondo.index', $rencontre)
            ->with('success', 'Combat terminé et résultat enregistré.');
    }

    public function destroy(Rencontre $rencontre, CombatTaekwondo $combat)
    {
        $combat->delete();

        return redirect()->route('combats-taekwondo.index', $rencontre)
            ->with('success', 'Combat supprimé.');
    }
}
