<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Paiement;
use App\Models\Performance;
use App\Models\SuiviScolaire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests des permissions admin/coach
 */
class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $coachUser;
    protected Coach $coach;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create();
        $this->coachUser = User::factory()->coach()->create();
        $this->coach = Coach::factory()->forUser($this->coachUser)->create();
    }

    // ==================== ATHLETES ====================

    public function test_admin_has_full_access_to_athletes(): void
    {
        $athlete = Athlete::factory()->create();

        // Index
        $this->actingAs($this->admin)->get(route('athletes.index'))->assertStatus(200);
        // Create
        $this->actingAs($this->admin)->get(route('athletes.create'))->assertStatus(200);
        // Show
        $this->actingAs($this->admin)->get(route('athletes.show', $athlete))->assertStatus(200);
        // Edit
        $this->actingAs($this->admin)->get(route('athletes.edit', $athlete))->assertStatus(200);
    }

    public function test_coach_has_read_only_access_to_athletes(): void
    {
        $athlete = Athlete::factory()->create();

        // Index - OK
        $this->actingAs($this->coachUser)->get(route('athletes.index'))->assertStatus(200);
        // Show - OK
        $this->actingAs($this->coachUser)->get(route('athletes.show', $athlete))->assertStatus(200);
        // Create - Forbidden
        $this->actingAs($this->coachUser)->get(route('athletes.create'))->assertStatus(403);
        // Edit - Forbidden
        $this->actingAs($this->coachUser)->get(route('athletes.edit', $athlete))->assertStatus(403);
        // Store - Forbidden
        $this->actingAs($this->coachUser)->post(route('athletes.store'), [])->assertStatus(403);
        // Update - Forbidden
        $this->actingAs($this->coachUser)->put(route('athletes.update', $athlete), [])->assertStatus(403);
        // Delete - Forbidden
        $this->actingAs($this->coachUser)->delete(route('athletes.destroy', $athlete))->assertStatus(403);
    }

    // ==================== COACHS ====================

    public function test_admin_has_full_access_to_coachs(): void
    {
        $coach = Coach::factory()->create();

        $this->actingAs($this->admin)->get(route('coachs.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('coachs.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('coachs.show', $coach))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('coachs.edit', $coach))->assertStatus(200);
    }

    public function test_coach_cannot_access_coachs_management(): void
    {
        $coach = Coach::factory()->create();

        $this->actingAs($this->coachUser)->get(route('coachs.index'))->assertStatus(403);
        $this->actingAs($this->coachUser)->get(route('coachs.create'))->assertStatus(403);
        $this->actingAs($this->coachUser)->get(route('coachs.show', $coach))->assertStatus(403);
    }

    // ==================== DISCIPLINES ====================

    public function test_admin_has_full_access_to_disciplines(): void
    {
        $discipline = Discipline::factory()->create();

        $this->actingAs($this->admin)->get(route('disciplines.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('disciplines.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('disciplines.show', $discipline))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('disciplines.edit', $discipline))->assertStatus(200);
    }

    public function test_coach_has_read_only_access_to_disciplines(): void
    {
        $discipline = Discipline::factory()->create();

        // Index - OK
        $this->actingAs($this->coachUser)->get(route('disciplines.index'))->assertStatus(200);
        // Show - OK
        $this->actingAs($this->coachUser)->get(route('disciplines.show', $discipline))->assertStatus(200);
        // Create - Forbidden
        $this->actingAs($this->coachUser)->get(route('disciplines.create'))->assertStatus(403);
        // Edit - Forbidden
        $this->actingAs($this->coachUser)->get(route('disciplines.edit', $discipline))->assertStatus(403);
    }

    // ==================== PRESENCES ====================

    public function test_admin_has_full_access_to_presences(): void
    {
        $this->actingAs($this->admin)->get(route('presences.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('presences.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('presences.rapport-mensuel'))->assertStatus(200);
    }

    public function test_coach_has_full_access_to_presences(): void
    {
        // Les coachs peuvent gérer les présences
        $this->actingAs($this->coachUser)->get(route('presences.index'))->assertStatus(200);
        $this->actingAs($this->coachUser)->get(route('presences.create'))->assertStatus(200);
        $this->actingAs($this->coachUser)->get(route('presences.rapport-mensuel'))->assertStatus(200);
    }

    // ==================== PAIEMENTS ====================

    public function test_admin_has_full_access_to_paiements(): void
    {
        $paiement = Paiement::factory()->create();

        $this->actingAs($this->admin)->get(route('paiements.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('paiements.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('paiements.show', $paiement))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('paiements.arrieres'))->assertStatus(200);
    }

    public function test_coach_cannot_access_paiements(): void
    {
        $paiement = Paiement::factory()->create();

        $this->actingAs($this->coachUser)->get(route('paiements.index'))->assertStatus(403);
        $this->actingAs($this->coachUser)->get(route('paiements.create'))->assertStatus(403);
        $this->actingAs($this->coachUser)->get(route('paiements.show', $paiement))->assertStatus(403);
    }

    // ==================== PERFORMANCES ====================

    public function test_admin_has_full_access_to_performances(): void
    {
        $performance = Performance::factory()->create();

        $this->actingAs($this->admin)->get(route('performances.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('performances.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('performances.show', $performance))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('performances.edit', $performance))->assertStatus(200);
    }

    public function test_coach_can_create_and_view_performances(): void
    {
        $performance = Performance::factory()->create();

        // Index - OK
        $this->actingAs($this->coachUser)->get(route('performances.index'))->assertStatus(200);
        // Create - OK
        $this->actingAs($this->coachUser)->get(route('performances.create'))->assertStatus(200);
        // Show - OK
        $this->actingAs($this->coachUser)->get(route('performances.show', $performance))->assertStatus(200);
        // Edit - Forbidden (admin only)
        $this->actingAs($this->coachUser)->get(route('performances.edit', $performance))->assertStatus(403);
    }

    // ==================== SUIVIS SCOLAIRES ====================

    public function test_admin_has_full_access_to_suivis_scolaires(): void
    {
        $athlete = Athlete::factory()->create();
        $suivi = SuiviScolaire::factory()->forAthlete($athlete)->create();

        $this->actingAs($this->admin)->get(route('suivis-scolaires.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('suivis-scolaires.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('suivis-scolaires.show', ['suivis_scolaire' => $suivi->id]))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('suivis-scolaires.edit', ['suivis_scolaire' => $suivi->id]))->assertStatus(200);
    }

    public function test_coach_cannot_access_suivis_scolaires(): void
    {
        $this->actingAs($this->coachUser)->get(route('suivis-scolaires.index'))->assertStatus(403);
        $this->actingAs($this->coachUser)->get(route('suivis-scolaires.create'))->assertStatus(403);
    }

    // ==================== DASHBOARD ====================

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard'))->assertStatus(200);
    }

    public function test_coach_can_access_dashboard(): void
    {
        $this->actingAs($this->coachUser)->get(route('dashboard'))->assertStatus(200);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    // ==================== USER ROLES ====================

    public function test_user_is_admin(): void
    {
        $this->assertTrue($this->admin->isAdmin());
        $this->assertFalse($this->admin->isCoach());
    }

    public function test_user_is_coach(): void
    {
        $this->assertTrue($this->coachUser->isCoach());
        $this->assertFalse($this->coachUser->isAdmin());
    }
}
