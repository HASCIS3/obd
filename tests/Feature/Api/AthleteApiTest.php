<?php

namespace Tests\Feature\Api;

use App\Models\Athlete;
use App\Models\Discipline;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AthleteApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_can_list_athletes(): void
    {
        Discipline::factory()->create();
        Athlete::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson('/api/athletes');

        $response->assertStatus(200);
    }

    public function test_can_show_athlete(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete = Athlete::factory()->create(['discipline_id' => $discipline->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->getJson("/api/athletes/{$athlete->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'nom', 'prenom']);
    }

    public function test_can_create_athlete(): void
    {
        $discipline = Discipline::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->postJson('/api/athletes', [
                'nom' => 'Diallo',
                'prenom' => 'Amadou',
                'date_naissance' => '2010-05-15',
                'sexe' => 'M',
                'discipline_id' => $discipline->id,
                'telephone_parent' => '70123456',
                'adresse' => 'Bamako',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('athletes', ['nom' => 'Diallo']);
    }

    public function test_can_update_athlete(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete = Athlete::factory()->create(['discipline_id' => $discipline->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->putJson("/api/athletes/{$athlete->id}", [
                'nom' => 'Nouveau Nom',
                'prenom' => $athlete->prenom,
                'date_naissance' => $athlete->date_naissance,
                'sexe' => $athlete->sexe,
                'discipline_id' => $discipline->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('athletes', ['nom' => 'Nouveau Nom']);
    }

    public function test_can_delete_athlete(): void
    {
        $discipline = Discipline::factory()->create();
        $athlete = Athlete::factory()->create(['discipline_id' => $discipline->id]);

        $response = $this->withHeader('Authorization', "Bearer {$this->token}")
            ->deleteJson("/api/athletes/{$athlete->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('athletes', ['id' => $athlete->id]);
    }
}
