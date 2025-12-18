<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AthleteControllerTest extends TestCase
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

    public function test_admin_can_view_athletes_list(): void
    {
        Athlete::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('athletes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('athletes.index');
        $response->assertViewHas('athletes');
    }

    public function test_coach_can_view_athletes_list(): void
    {
        $response = $this->actingAs($this->coach)->get(route('athletes.index'));

        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_athletes_list(): void
    {
        $response = $this->get(route('athletes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_athletes_list_can_be_filtered_by_search(): void
    {
        Athlete::factory()->create(['nom' => 'Diallo', 'prenom' => 'Amadou']);
        Athlete::factory()->create(['nom' => 'Traore', 'prenom' => 'Moussa']);

        $response = $this->actingAs($this->admin)
            ->get(route('athletes.index', ['search' => 'Diallo']));

        $response->assertStatus(200);
        $response->assertSee('Diallo');
        $response->assertDontSee('Traore');
    }

    public function test_athletes_list_can_be_filtered_by_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete1 = Athlete::factory()->create();
        $athlete2 = Athlete::factory()->create();

        $athlete1->disciplines()->attach($discipline->id, [
            'date_inscription' => now(),
            'actif' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('athletes.index', ['discipline' => $discipline->id]));

        $response->assertStatus(200);
    }

    // ==================== CREATE ====================

    public function test_admin_can_view_create_athlete_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('athletes.create'));

        $response->assertStatus(200);
        $response->assertViewIs('athletes.create');
    }

    public function test_coach_cannot_view_create_athlete_form(): void
    {
        $response = $this->actingAs($this->coach)->get(route('athletes.create'));

        $response->assertStatus(403);
    }

    // ==================== STORE ====================

    public function test_admin_can_create_athlete(): void
    {
        $discipline = Discipline::factory()->create();

        $data = [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'date_naissance' => '2010-05-15',
            'sexe' => 'M',
            'telephone' => '70123456',
            'email' => 'amadou@test.com',
            'adresse' => 'Bamako',
            'nom_tuteur' => 'Diallo Papa',
            'telephone_tuteur' => '70654321',
            'disciplines' => [$discipline->id],
        ];

        $response = $this->actingAs($this->admin)->post(route('athletes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('athletes', [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
        ]);
    }

    public function test_admin_can_create_athlete_with_photo(): void
    {
        Storage::fake('public');

        $data = [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ];

        $response = $this->actingAs($this->admin)->post(route('athletes.store'), $data);

        $response->assertRedirect();
        $athlete = Athlete::where('nom', 'Diallo')->first();
        $this->assertNotNull($athlete->photo);
        Storage::disk('public')->assertExists($athlete->photo);
    }

    public function test_coach_cannot_create_athlete(): void
    {
        $data = [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
        ];

        $response = $this->actingAs($this->coach)->post(route('athletes.store'), $data);

        $response->assertStatus(403);
    }

    public function test_create_athlete_validation_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), []);

        $response->assertSessionHasErrors(['nom', 'prenom', 'sexe']);
    }

    public function test_create_athlete_validation_sexe(): void
    {
        $data = [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'X', // Invalid
        ];

        $response = $this->actingAs($this->admin)->post(route('athletes.store'), $data);

        $response->assertSessionHasErrors(['sexe']);
    }

    public function test_create_athlete_validation_email(): void
    {
        $data = [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
            'email' => 'invalid-email',
        ];

        $response = $this->actingAs($this->admin)->post(route('athletes.store'), $data);

        $response->assertSessionHasErrors(['email']);
    }

    // ==================== SHOW ====================

    public function test_admin_can_view_athlete(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('athletes.show', $athlete));

        $response->assertStatus(200);
        $response->assertViewIs('athletes.show');
        $response->assertViewHas('athlete');
    }

    public function test_coach_can_view_athlete(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->coach)->get(route('athletes.show', $athlete));

        $response->assertStatus(200);
    }

    // ==================== EDIT ====================

    public function test_admin_can_view_edit_athlete_form(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('athletes.edit', $athlete));

        $response->assertStatus(200);
        $response->assertViewIs('athletes.edit');
    }

    public function test_coach_cannot_view_edit_athlete_form(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->coach)->get(route('athletes.edit', $athlete));

        $response->assertStatus(403);
    }

    // ==================== UPDATE ====================

    public function test_admin_can_update_athlete(): void
    {
        $athlete = Athlete::factory()->create();

        $data = [
            'nom' => 'Nouveau Nom',
            'prenom' => $athlete->prenom,
            'sexe' => $athlete->sexe,
        ];

        $response = $this->actingAs($this->admin)->put(route('athletes.update', $athlete), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('athletes', [
            'id' => $athlete->id,
            'nom' => 'Nouveau Nom',
        ]);
    }

    public function test_coach_cannot_update_athlete(): void
    {
        $athlete = Athlete::factory()->create();

        $data = [
            'nom' => 'Nouveau Nom',
            'prenom' => $athlete->prenom,
            'sexe' => $athlete->sexe,
        ];

        $response = $this->actingAs($this->coach)->put(route('athletes.update', $athlete), $data);

        $response->assertStatus(403);
    }

    // ==================== DESTROY ====================

    public function test_admin_can_delete_athlete(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('athletes.destroy', $athlete));

        $response->assertRedirect(route('athletes.index'));
        $this->assertDatabaseMissing('athletes', ['id' => $athlete->id]);
    }

    public function test_coach_cannot_delete_athlete(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->coach)->delete(route('athletes.destroy', $athlete));

        $response->assertStatus(403);
        $this->assertDatabaseHas('athletes', ['id' => $athlete->id]);
    }
}
