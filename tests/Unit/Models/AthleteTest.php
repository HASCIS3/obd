<?php

namespace Tests\Unit\Models;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Paiement;
use App\Models\Performance;
use App\Models\Presence;
use App\Models\SuiviScolaire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AthleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_athlete_can_be_created(): void
    {
        $athlete = Athlete::factory()->create();

        $this->assertDatabaseHas('athletes', [
            'id' => $athlete->id,
            'nom' => $athlete->nom,
            'prenom' => $athlete->prenom,
        ]);
    }

    public function test_athlete_has_nom_complet_attribute(): void
    {
        $athlete = Athlete::factory()->create([
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
        ]);

        $this->assertEquals('Amadou Diallo', $athlete->nom_complet);
    }

    public function test_athlete_has_age_attribute(): void
    {
        $athlete = Athlete::factory()->create([
            'date_naissance' => now()->subYears(15),
        ]);

        $this->assertEquals(15, $athlete->age);
    }

    public function test_athlete_has_categorie_age_attribute(): void
    {
        $poussin = Athlete::factory()->create(['date_naissance' => now()->subYears(8)]);
        $benjamin = Athlete::factory()->create(['date_naissance' => now()->subYears(11)]);
        $minime = Athlete::factory()->create(['date_naissance' => now()->subYears(14)]);
        $cadet = Athlete::factory()->create(['date_naissance' => now()->subYears(16)]);
        $junior = Athlete::factory()->create(['date_naissance' => now()->subYears(19)]);
        $senior = Athlete::factory()->create(['date_naissance' => now()->subYears(25)]);

        $this->assertEquals('Poussin', $poussin->categorie_age);
        $this->assertEquals('Benjamin', $benjamin->categorie_age);
        $this->assertEquals('Minime', $minime->categorie_age);
        $this->assertEquals('Cadet', $cadet->categorie_age);
        $this->assertEquals('Junior', $junior->categorie_age);
        $this->assertEquals('Senior', $senior->categorie_age);
    }

    public function test_athlete_can_have_disciplines(): void
    {
        $athlete = Athlete::factory()->create();
        $discipline = Discipline::factory()->create();

        $athlete->disciplines()->attach($discipline->id, [
            'date_inscription' => now(),
            'actif' => true,
        ]);

        $this->assertTrue($athlete->disciplines->contains($discipline));
        $this->assertEquals(1, $athlete->disciplines()->count());
    }

    public function test_athlete_can_have_presences(): void
    {
        $athlete = Athlete::factory()->create();
        $presence = Presence::factory()->forAthlete($athlete)->create();

        $this->assertTrue($athlete->presences->contains($presence));
    }

    public function test_athlete_can_have_paiements(): void
    {
        $athlete = Athlete::factory()->create();
        $paiement = Paiement::factory()->forAthlete($athlete)->create();

        $this->assertTrue($athlete->paiements->contains($paiement));
    }

    public function test_athlete_can_have_performances(): void
    {
        $athlete = Athlete::factory()->create();
        $performance = Performance::factory()->forAthlete($athlete)->create();

        $this->assertTrue($athlete->performances->contains($performance));
    }

    public function test_athlete_can_have_suivi_scolaire(): void
    {
        $athlete = Athlete::factory()->create();
        $suivi = SuiviScolaire::factory()->forAthlete($athlete)->create();

        $this->assertNotNull($athlete->suiviScolaire);
        $this->assertEquals($suivi->id, $athlete->suiviScolaire->id);
    }

    public function test_athlete_arrieres_calculation(): void
    {
        $athlete = Athlete::factory()->create();
        
        // Paiement impayé
        Paiement::factory()->forAthlete($athlete)->impaye()->create([
            'montant' => 20000,
        ]);
        
        // Paiement partiel
        Paiement::factory()->forAthlete($athlete)->create([
            'montant' => 15000,
            'montant_paye' => 5000,
            'statut' => Paiement::STATUT_PARTIEL,
        ]);

        // Total arriérés = 20000 + (15000 - 5000) = 30000
        $this->assertEquals(30000, $athlete->arrieres);
    }

    public function test_athlete_est_a_jour_paiements(): void
    {
        $athlete = Athlete::factory()->create();
        
        // Pas de paiements = à jour
        $this->assertTrue($athlete->estAJourPaiements());

        // Ajouter un paiement impayé
        Paiement::factory()->forAthlete($athlete)->impaye()->create();
        $athlete->refresh();

        $this->assertFalse($athlete->estAJourPaiements());
    }

    public function test_athlete_taux_presence(): void
    {
        $athlete = Athlete::factory()->create();
        $discipline = Discipline::factory()->create();

        // 7 présences, 3 absences = 70%
        for ($i = 0; $i < 7; $i++) {
            Presence::factory()->forAthlete($athlete)->forDiscipline($discipline)->present()->create();
        }
        for ($i = 0; $i < 3; $i++) {
            Presence::factory()->forAthlete($athlete)->forDiscipline($discipline)->absent()->create();
        }

        $this->assertEquals(70.0, $athlete->taux_presence);
    }

    public function test_athlete_est_mineur(): void
    {
        $mineur = Athlete::factory()->minor()->create();
        $majeur = Athlete::factory()->adult()->create();

        $this->assertTrue($mineur->estMineur());
        $this->assertFalse($majeur->estMineur());
    }

    public function test_athlete_scope_actifs(): void
    {
        Athlete::factory()->count(3)->create(['actif' => true]);
        Athlete::factory()->count(2)->create(['actif' => false]);

        $this->assertEquals(3, Athlete::actifs()->count());
    }

    public function test_athlete_scope_avec_arrieres(): void
    {
        $athleteAvecArrieres = Athlete::factory()->create();
        Paiement::factory()->forAthlete($athleteAvecArrieres)->impaye()->create();

        $athleteSansArrieres = Athlete::factory()->create();
        Paiement::factory()->forAthlete($athleteSansArrieres)->create(); // payé

        $this->assertEquals(1, Athlete::avecArrieres()->count());
    }

    public function test_athlete_scope_mineurs(): void
    {
        Athlete::factory()->minor()->count(2)->create();
        Athlete::factory()->adult()->count(3)->create();

        $this->assertEquals(2, Athlete::mineurs()->count());
    }

    public function test_athlete_est_inscrit_a_discipline(): void
    {
        $athlete = Athlete::factory()->create();
        $discipline1 = Discipline::factory()->create();
        $discipline2 = Discipline::factory()->create();

        $athlete->disciplines()->attach($discipline1->id, [
            'date_inscription' => now(),
            'actif' => true,
        ]);

        $this->assertTrue($athlete->estInscritA($discipline1));
        $this->assertFalse($athlete->estInscritA($discipline2));
    }

    public function test_athlete_tarif_mensuel_total(): void
    {
        $athlete = Athlete::factory()->create();
        $discipline1 = Discipline::factory()->create(['tarif_mensuel' => 15000]);
        $discipline2 = Discipline::factory()->create(['tarif_mensuel' => 10000]);

        $athlete->disciplines()->attach([
            $discipline1->id => ['date_inscription' => now(), 'actif' => true],
            $discipline2->id => ['date_inscription' => now(), 'actif' => true],
        ]);

        $this->assertEquals(25000, $athlete->getTarifMensuelTotal());
    }
}
