<?php

namespace Tests\Unit\Models;

use App\Models\Athlete;
use App\Models\Paiement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementTest extends TestCase
{
    use RefreshDatabase;

    public function test_paiement_can_be_created(): void
    {
        $paiement = Paiement::factory()->create();

        $this->assertDatabaseHas('paiements', [
            'id' => $paiement->id,
        ]);
    }

    public function test_paiement_belongs_to_athlete(): void
    {
        $athlete = Athlete::factory()->create();
        $paiement = Paiement::factory()->forAthlete($athlete)->create();

        $this->assertEquals($athlete->id, $paiement->athlete->id);
    }

    public function test_paiement_reste_a_payer(): void
    {
        $paiement = Paiement::factory()->create([
            'montant' => 20000,
            'montant_paye' => 15000,
        ]);

        $this->assertEquals(5000, $paiement->reste_a_payer);
    }

    public function test_paiement_periode_attribute(): void
    {
        $paiement = Paiement::factory()->create([
            'mois' => 3,
            'annee' => 2024,
        ]);

        $this->assertEquals('Mars 2024', $paiement->periode);
    }

    public function test_paiement_pourcentage_paye(): void
    {
        $paiement = Paiement::factory()->create([
            'montant' => 20000,
            'montant_paye' => 15000,
        ]);

        $this->assertEquals(75.0, $paiement->pourcentage_paye);
    }

    public function test_paiement_est_complet(): void
    {
        $paye = Paiement::factory()->create(['statut' => Paiement::STATUT_PAYE]);
        $impaye = Paiement::factory()->impaye()->create();
        $partiel = Paiement::factory()->partiel()->create();

        $this->assertTrue($paye->estComplet());
        $this->assertFalse($impaye->estComplet());
        $this->assertFalse($partiel->estComplet());
    }

    public function test_paiement_est_en_retard(): void
    {
        // Paiement du mois dernier non payé
        $enRetard = Paiement::factory()->impaye()->create([
            'mois' => now()->subMonth()->month,
            'annee' => now()->subMonth()->year,
        ]);

        // Paiement du mois en cours non payé
        $pasDuToutEnRetard = Paiement::factory()->impaye()->create([
            'mois' => now()->month,
            'annee' => now()->year,
        ]);

        // Paiement payé
        $paye = Paiement::factory()->create([
            'statut' => Paiement::STATUT_PAYE,
        ]);

        $this->assertTrue($enRetard->estEnRetard());
        $this->assertFalse($paye->estEnRetard());
    }

    public function test_paiement_determiner_statut(): void
    {
        $this->assertEquals(Paiement::STATUT_PAYE, Paiement::determinerStatut(20000, 20000));
        $this->assertEquals(Paiement::STATUT_PAYE, Paiement::determinerStatut(20000, 25000)); // Surpaiement
        $this->assertEquals(Paiement::STATUT_PARTIEL, Paiement::determinerStatut(20000, 10000));
        $this->assertEquals(Paiement::STATUT_IMPAYE, Paiement::determinerStatut(20000, 0));
    }

    public function test_paiement_enregistrer_paiement(): void
    {
        $paiement = Paiement::factory()->impaye()->create([
            'montant' => 20000,
        ]);

        // Paiement partiel
        $paiement->enregistrerPaiement(10000, Paiement::MODE_MOBILE, 'REF123');
        
        $this->assertEquals(10000, $paiement->montant_paye);
        $this->assertEquals(Paiement::STATUT_PARTIEL, $paiement->statut);
        $this->assertEquals('REF123', $paiement->reference);

        // Compléter le paiement
        $paiement->enregistrerPaiement(10000);
        
        $this->assertEquals(20000, $paiement->montant_paye);
        $this->assertEquals(Paiement::STATUT_PAYE, $paiement->statut);
    }

    public function test_paiement_scope_payes(): void
    {
        Paiement::factory()->count(3)->create(['statut' => Paiement::STATUT_PAYE]);
        Paiement::factory()->count(2)->impaye()->create();

        $this->assertEquals(3, Paiement::payes()->count());
    }

    public function test_paiement_scope_arrieres(): void
    {
        Paiement::factory()->count(2)->create(['statut' => Paiement::STATUT_PAYE]);
        Paiement::factory()->count(3)->impaye()->create();
        Paiement::factory()->count(1)->partiel()->create();

        $this->assertEquals(4, Paiement::arrieres()->count());
    }

    public function test_paiement_scope_pour_periode(): void
    {
        Paiement::factory()->pourPeriode(6, 2024)->count(2)->create();
        Paiement::factory()->pourPeriode(7, 2024)->count(3)->create();

        $this->assertEquals(2, Paiement::pourPeriode(6, 2024)->count());
        $this->assertEquals(3, Paiement::pourPeriode(7, 2024)->count());
    }

    public function test_paiement_statuts_list(): void
    {
        $statuts = Paiement::statuts();

        $this->assertArrayHasKey(Paiement::STATUT_PAYE, $statuts);
        $this->assertArrayHasKey(Paiement::STATUT_IMPAYE, $statuts);
        $this->assertArrayHasKey(Paiement::STATUT_PARTIEL, $statuts);
    }

    public function test_paiement_modes_paiement_list(): void
    {
        $modes = Paiement::modesPaiement();

        $this->assertArrayHasKey(Paiement::MODE_ESPECES, $modes);
        $this->assertArrayHasKey(Paiement::MODE_VIREMENT, $modes);
        $this->assertArrayHasKey(Paiement::MODE_MOBILE, $modes);
    }
}
