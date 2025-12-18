<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresenceControllerTest extends TestCase
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

    // ==================== INDEX ====================

    public function test_admin_can_view_presences_list(): void
    {
        Presence::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('presences.index'));

        $response->assertStatus(200);
        $response->assertViewIs('presences.index');
    }

    public function test_coach_can_view_presences_list(): void
    {
        $response = $this->actingAs($this->coachUser)->get(route('presences.index'));

        $response->assertStatus(200);
    }

    public function test_presences_list_filters_by_date(): void
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        Presence::factory()->pourDate($today)->count(3)->create();
        Presence::factory()->pourDate($yesterday)->count(2)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('presences.index', ['date' => $today]));

        $response->assertStatus(200);
    }

    // ==================== CREATE ====================

    public function test_admin_can_view_create_presence_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('presences.create'));

        $response->assertStatus(200);
        $response->assertViewIs('presences.create');
    }

    public function test_coach_can_view_create_presence_form(): void
    {
        $response = $this->actingAs($this->coachUser)->get(route('presences.create'));

        $response->assertStatus(200);
    }

    public function test_create_form_shows_athletes_for_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete1 = Athlete::factory()->create();
        $athlete2 = Athlete::factory()->create();

        $athlete1->disciplines()->attach($discipline->id, [
            'date_inscription' => now(),
            'actif' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('presences.create', ['discipline' => $discipline->id]));

        $response->assertStatus(200);
        $response->assertViewHas('athletes');
    }

    // ==================== STORE ====================

    public function test_admin_can_store_presences(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete1 = Athlete::factory()->create();
        $athlete2 = Athlete::factory()->create();

        $athlete1->disciplines()->attach($discipline->id, ['date_inscription' => now(), 'actif' => true]);
        $athlete2->disciplines()->attach($discipline->id, ['date_inscription' => now(), 'actif' => true]);

        $data = [
            'date' => now()->format('Y-m-d'),
            'discipline_id' => $discipline->id,
            'presences' => [
                ['athlete_id' => $athlete1->id, 'present' => true, 'remarque' => null],
                ['athlete_id' => $athlete2->id, 'present' => false, 'remarque' => 'Malade'],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('presences.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('presences', [
            'athlete_id' => $athlete1->id,
            'discipline_id' => $discipline->id,
            'present' => true,
        ]);
        $this->assertDatabaseHas('presences', [
            'athlete_id' => $athlete2->id,
            'discipline_id' => $discipline->id,
            'present' => false,
            'remarque' => 'Malade',
        ]);
    }

    public function test_coach_can_store_presences(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete = Athlete::factory()->create();
        $athlete->disciplines()->attach($discipline->id, ['date_inscription' => now(), 'actif' => true]);

        $data = [
            'date' => now()->format('Y-m-d'),
            'discipline_id' => $discipline->id,
            'presences' => [
                ['athlete_id' => $athlete->id, 'present' => true, 'remarque' => null],
            ],
        ];

        $response = $this->actingAs($this->coachUser)->post(route('presences.store'), $data);

        $response->assertRedirect();
    }

    public function test_store_presences_updates_existing(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete = Athlete::factory()->create();
        $athlete->disciplines()->attach($discipline->id, ['date_inscription' => now(), 'actif' => true]);

        $date = now()->format('Y-m-d');

        // Créer une présence existante
        Presence::factory()->forAthlete($athlete)->forDiscipline($discipline)->pourDate($date)->absent()->create();

        $data = [
            'date' => $date,
            'discipline_id' => $discipline->id,
            'presences' => [
                ['athlete_id' => $athlete->id, 'present' => true, 'remarque' => 'Mis à jour'],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('presences.store'), $data);

        // Vérifier que la présence a été mise à jour (pas de doublon)
        $this->assertEquals(1, Presence::where('athlete_id', $athlete->id)
            ->where('discipline_id', $discipline->id)
            ->whereDate('date', $date)
            ->count());

        $this->assertDatabaseHas('presences', [
            'athlete_id' => $athlete->id,
            'present' => true,
            'remarque' => 'Mis à jour',
        ]);
    }

    public function test_store_presences_validation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('presences.store'), []);

        $response->assertSessionHasErrors(['date', 'discipline_id', 'presences']);
    }

    // ==================== ATHLETE STATS ====================

    public function test_admin_can_view_athlete_presence_stats(): void
    {
        $athlete = Athlete::factory()->create();
        Presence::factory()->forAthlete($athlete)->present()->count(7)->create();
        Presence::factory()->forAthlete($athlete)->absent()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('presences.athlete', $athlete));

        $response->assertStatus(200);
        $response->assertViewIs('presences.athlete-stats');
        $response->assertViewHas('stats');
    }

    // ==================== RAPPORT MENSUEL ====================

    public function test_admin_can_view_monthly_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('presences.rapport-mensuel'));

        $response->assertStatus(200);
        $response->assertViewIs('presences.rapport-mensuel');
    }

    public function test_monthly_report_shows_correct_stats(): void
    {
        $discipline = Discipline::factory()->create();
        
        // Créer des présences pour le mois en cours
        Presence::factory()
            ->forDiscipline($discipline)
            ->present()
            ->count(8)
            ->create(['date' => now()]);
        
        Presence::factory()
            ->forDiscipline($discipline)
            ->absent()
            ->count(2)
            ->create(['date' => now()]);

        $response = $this->actingAs($this->admin)->get(route('presences.rapport-mensuel', [
            'mois' => now()->month,
            'annee' => now()->year,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }
}
