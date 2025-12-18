<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tests des validations de formulaires
 */
class ValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    // ==================== ATHLETE VALIDATION ====================

    public function test_athlete_nom_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'prenom' => 'Amadou',
            'sexe' => 'M',
        ]);

        $response->assertSessionHasErrors('nom');
    }

    public function test_athlete_prenom_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'sexe' => 'M',
        ]);

        $response->assertSessionHasErrors('prenom');
    }

    public function test_athlete_sexe_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
        ]);

        $response->assertSessionHasErrors('sexe');
    }

    public function test_athlete_sexe_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'X', // Invalid
        ]);

        $response->assertSessionHasErrors('sexe');
    }

    public function test_athlete_email_must_be_valid(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_athlete_date_naissance_must_be_before_today(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
            'date_naissance' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('date_naissance');
    }

    public function test_athlete_photo_must_be_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
            'photo' => UploadedFile::fake()->create('document.pdf', 100),
        ]);

        $response->assertSessionHasErrors('photo');
    }

    public function test_athlete_photo_max_size(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
            'photo' => UploadedFile::fake()->image('photo.jpg')->size(3000), // 3MB > 2MB limit
        ]);

        $response->assertSessionHasErrors('photo');
    }

    public function test_athlete_disciplines_must_exist(): void
    {
        $response = $this->actingAs($this->admin)->post(route('athletes.store'), [
            'nom' => 'Diallo',
            'prenom' => 'Amadou',
            'sexe' => 'M',
            'disciplines' => [999], // Non-existent
        ]);

        $response->assertSessionHasErrors('disciplines.0');
    }

    // ==================== PAIEMENT VALIDATION ====================

    public function test_paiement_athlete_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('paiements.store'), [
            'montant' => 20000,
            'montant_paye' => 20000,
            'mois' => 6,
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);

        $response->assertSessionHasErrors('athlete_id');
    }

    public function test_paiement_athlete_must_exist(): void
    {
        $response = $this->actingAs($this->admin)->post(route('paiements.store'), [
            'athlete_id' => 999,
            'montant' => 20000,
            'montant_paye' => 20000,
            'mois' => 6,
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);

        $response->assertSessionHasErrors('athlete_id');
    }

    public function test_paiement_montant_must_be_positive(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('paiements.store'), [
            'athlete_id' => $athlete->id,
            'montant' => -1000,
            'montant_paye' => 0,
            'mois' => 6,
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);

        $response->assertSessionHasErrors('montant');
    }

    public function test_paiement_mois_must_be_valid(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('paiements.store'), [
            'athlete_id' => $athlete->id,
            'montant' => 20000,
            'montant_paye' => 20000,
            'mois' => 13, // Invalid
            'annee' => 2024,
            'mode_paiement' => Paiement::MODE_ESPECES,
        ]);

        $response->assertSessionHasErrors('mois');
    }

    public function test_paiement_mode_must_be_valid(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('paiements.store'), [
            'athlete_id' => $athlete->id,
            'montant' => 20000,
            'montant_paye' => 20000,
            'mois' => 6,
            'annee' => 2024,
            'mode_paiement' => 'bitcoin', // Invalid
        ]);

        $response->assertSessionHasErrors('mode_paiement');
    }

    // ==================== PRESENCE VALIDATION ====================

    public function test_presence_date_is_required(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('presences.store'), [
            'discipline_id' => $discipline->id,
            'presences' => [
                ['athlete_id' => $athlete->id, 'present' => true],
            ],
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_presence_discipline_is_required(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('presences.store'), [
            'date' => now()->format('Y-m-d'),
            'presences' => [
                ['athlete_id' => $athlete->id, 'present' => true],
            ],
        ]);

        $response->assertSessionHasErrors('discipline_id');
    }

    public function test_presence_discipline_must_exist(): void
    {
        $athlete = Athlete::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('presences.store'), [
            'date' => now()->format('Y-m-d'),
            'discipline_id' => 999,
            'presences' => [
                ['athlete_id' => $athlete->id, 'present' => true],
            ],
        ]);

        $response->assertSessionHasErrors('discipline_id');
    }

    public function test_presence_presences_is_required(): void
    {
        $discipline = Discipline::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('presences.store'), [
            'date' => now()->format('Y-m-d'),
            'discipline_id' => $discipline->id,
        ]);

        $response->assertSessionHasErrors('presences');
    }

    public function test_presence_athlete_must_exist(): void
    {
        $discipline = Discipline::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('presences.store'), [
            'date' => now()->format('Y-m-d'),
            'discipline_id' => $discipline->id,
            'presences' => [
                ['athlete_id' => 999, 'present' => true],
            ],
        ]);

        $response->assertSessionHasErrors('presences.0.athlete_id');
    }

    // ==================== DISCIPLINE VALIDATION ====================

    public function test_discipline_nom_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('disciplines.store'), [
            'tarif_mensuel' => 15000,
        ]);

        $response->assertSessionHasErrors('nom');
    }

    public function test_discipline_nom_must_be_unique(): void
    {
        Discipline::factory()->create(['nom' => 'Football']);

        $response = $this->actingAs($this->admin)->post(route('disciplines.store'), [
            'nom' => 'Football',
            'tarif_mensuel' => 15000,
        ]);

        $response->assertSessionHasErrors('nom');
    }

    public function test_discipline_tarif_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('disciplines.store'), [
            'nom' => 'Basketball',
        ]);

        $response->assertSessionHasErrors('tarif_mensuel');
    }

    public function test_discipline_tarif_must_be_positive(): void
    {
        $response = $this->actingAs($this->admin)->post(route('disciplines.store'), [
            'nom' => 'Basketball',
            'tarif_mensuel' => -1000,
        ]);

        $response->assertSessionHasErrors('tarif_mensuel');
    }

    // ==================== COACH VALIDATION ====================

    public function test_coach_name_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('coachs.store'), [
            'email' => 'coach@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_coach_email_is_required(): void
    {
        $response = $this->actingAs($this->admin)->post(route('coachs.store'), [
            'name' => 'Coach Test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_coach_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $response = $this->actingAs($this->admin)->post(route('coachs.store'), [
            'name' => 'Coach Test',
            'email' => 'existing@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_coach_password_must_be_confirmed(): void
    {
        $response = $this->actingAs($this->admin)->post(route('coachs.store'), [
            'name' => 'Coach Test',
            'email' => 'coach@test.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_coach_password_min_length(): void
    {
        $response = $this->actingAs($this->admin)->post(route('coachs.store'), [
            'name' => 'Coach Test',
            'email' => 'coach@test.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
