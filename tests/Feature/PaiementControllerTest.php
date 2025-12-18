<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $coach;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create();
        $this->coach = User::factory()->coach()->create();
    }

    // ==================== INDEX ====================

    public function test_admin_can_view_paiements_list(): void
    {
        Paiement::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('paiements.index'));

        $response->assertStatus(200);
        $response->assertViewIs('paiements.index');
        $response->assertViewHas('paiements');
        $response->assertViewHas('stats');
    }

    public function test_coach_cannot_view_paiements_list(): void
    {
        $response = $this->actingAs($this->coach)->get(route('paiements.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_view_paiements_list(): void
    {
        $response = $this->get(route('paiements.index'));

        $response->assertRedirect(route('login'));
    }

    // ==================== CREATE ====================

    public function test_admin_can_view_create_paiement_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('paiements.create'));

        $response->assertStatus(200);
        $response->assertViewIs('paiements.create');
    }

    // ==================== STORE ====================

    public function test_admin_can_create_paiement(): void
    {
        $athlete = Athlete::factory()->create();

        $data = [
            'athlete_id' => $athlete->id,
            'montant' => 20000,
            'montant_paye' => 20000,
            'mois' => 6,
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ];

        $response = $this->actingAs($this->admin)->post(route('paiements.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('paiements', [
            'athlete_id' => $athlete->id,
            'montant' => 20000,
            'statut' => Paiement::STATUT_PAYE,
        ]);
    }

    public function test_paiement_statut_is_auto_calculated(): void
    {
        $athlete = Athlete::factory()->create();

        // Paiement complet
        $this->actingAs($this->admin)->post(route('paiements.store'), [
            'athlete_id' => $athlete->id,
            'montant' => 20000,
            'montant_paye' => 20000,
            'mois' => 6,
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);

        $this->assertDatabaseHas('paiements', [
            'athlete_id' => $athlete->id,
            'mois' => 6,
            'statut' => Paiement::STATUT_PAYE,
        ]);

        // Paiement partiel
        $this->actingAs($this->admin)->post(route('paiements.store'), [
            'athlete_id' => $athlete->id,
            'montant' => 20000,
            'montant_paye' => 10000,
            'mois' => 7,
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);

        $this->assertDatabaseHas('paiements', [
            'athlete_id' => $athlete->id,
            'mois' => 7,
            'statut' => Paiement::STATUT_PARTIEL,
        ]);

        // Impayé
        $this->actingAs($this->admin)->post(route('paiements.store'), [
            'athlete_id' => $athlete->id,
            'montant' => 20000,
            'montant_paye' => 0,
            'mois' => 8,
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);

        $this->assertDatabaseHas('paiements', [
            'athlete_id' => $athlete->id,
            'mois' => 8,
            'statut' => Paiement::STATUT_IMPAYE,
        ]);
    }

    public function test_create_paiement_validation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('paiements.store'), []);

        $response->assertSessionHasErrors(['athlete_id', 'montant', 'montant_paye', 'mois', 'annee', 'mode_paiement']);
    }

    // ==================== SHOW ====================

    public function test_admin_can_view_paiement(): void
    {
        $paiement = Paiement::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('paiements.show', $paiement));

        $response->assertStatus(200);
        $response->assertViewIs('paiements.show');
    }

    // ==================== UPDATE ====================

    public function test_admin_can_update_paiement(): void
    {
        $paiement = Paiement::factory()->impaye()->create([
            'montant' => 20000,
        ]);

        $data = [
            'montant' => 20000,
            'montant_paye' => 15000,
            'mois' => $paiement->mois,
            'annee' => $paiement->annee,
            'mode_paiement' => Paiement::MODE_MOBILE,
        ];

        $response = $this->actingAs($this->admin)->put(route('paiements.update', $paiement), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('paiements', [
            'id' => $paiement->id,
            'montant_paye' => 15000,
            'statut' => Paiement::STATUT_PARTIEL,
        ]);
    }

    // ==================== DESTROY ====================

    public function test_admin_can_delete_paiement(): void
    {
        $paiement = Paiement::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('paiements.destroy', $paiement));

        $response->assertRedirect(route('paiements.index'));
        $this->assertDatabaseMissing('paiements', ['id' => $paiement->id]);
    }

    // ==================== ARRIERES ====================

    public function test_admin_can_view_arrieres(): void
    {
        // Créer des paiements avec arriérés
        Paiement::factory()->impaye()->count(3)->create();
        Paiement::factory()->partiel()->count(2)->create();

        $response = $this->actingAs($this->admin)->get(route('paiements.arrieres'));

        $response->assertStatus(200);
        $response->assertViewIs('paiements.arrieres');
    }

    // ==================== GENERER MENSUEL ====================

    public function test_admin_can_generate_monthly_payments(): void
    {
        // Créer des athlètes avec disciplines
        $discipline = \App\Models\Discipline::factory()->create(['tarif_mensuel' => 15000]);
        $athlete1 = Athlete::factory()->create(['actif' => true]);
        $athlete2 = Athlete::factory()->create(['actif' => true]);

        $athlete1->disciplines()->attach($discipline->id, ['date_inscription' => now(), 'actif' => true]);
        $athlete2->disciplines()->attach($discipline->id, ['date_inscription' => now(), 'actif' => true]);

        $response = $this->actingAs($this->admin)->post(route('paiements.generer-mensuel'), [
            'mois' => 12,
            'annee' => 2024,
        ]);

        $response->assertRedirect(route('paiements.index'));
        $response->assertSessionHas('success');

        // Vérifier que les paiements ont été créés
        $this->assertEquals(2, Paiement::where('mois', 12)->where('annee', 2024)->count());
    }

    public function test_generate_monthly_payments_does_not_duplicate(): void
    {
        $discipline = \App\Models\Discipline::factory()->create(['tarif_mensuel' => 15000]);
        $athlete = Athlete::factory()->create(['actif' => true]);
        $athlete->disciplines()->attach($discipline->id, ['date_inscription' => now(), 'actif' => true]);

        // Créer un paiement existant
        Paiement::factory()->forAthlete($athlete)->pourPeriode(12, 2024)->create();

        $response = $this->actingAs($this->admin)->post(route('paiements.generer-mensuel'), [
            'mois' => 12,
            'annee' => 2024,
        ]);

        // Ne devrait pas créer de doublon
        $this->assertEquals(1, Paiement::where('athlete_id', $athlete->id)
            ->where('mois', 12)
            ->where('annee', 2024)
            ->count());
    }
}
