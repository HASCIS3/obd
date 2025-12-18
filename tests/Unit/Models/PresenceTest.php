<?php

namespace Tests\Unit\Models;

use App\Models\Athlete;
use App\Models\Coach;
use App\Models\Discipline;
use App\Models\Presence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_presence_can_be_created(): void
    {
        $presence = Presence::factory()->create();

        $this->assertDatabaseHas('presences', [
            'id' => $presence->id,
        ]);
    }

    public function test_presence_belongs_to_athlete(): void
    {
        $athlete = Athlete::factory()->create();
        $presence = Presence::factory()->forAthlete($athlete)->create();

        $this->assertEquals($athlete->id, $presence->athlete->id);
    }

    public function test_presence_belongs_to_discipline(): void
    {
        $discipline = Discipline::factory()->create();
        $presence = Presence::factory()->forDiscipline($discipline)->create();

        $this->assertEquals($discipline->id, $presence->discipline->id);
    }

    public function test_presence_can_belong_to_coach(): void
    {
        $coach = Coach::factory()->create();
        $presence = Presence::factory()->byCoach($coach)->create();

        $this->assertEquals($coach->id, $presence->coach->id);
    }

    public function test_presence_statut_libelle(): void
    {
        $present = Presence::factory()->present()->create();
        $absent = Presence::factory()->absent()->create();

        $this->assertEquals('Présent', $present->statut_libelle);
        $this->assertEquals('Absent', $absent->statut_libelle);
    }

    public function test_presence_statut_couleur(): void
    {
        $present = Presence::factory()->present()->create();
        $absent = Presence::factory()->absent()->create();

        $this->assertEquals('success', $present->statut_couleur);
        $this->assertEquals('danger', $absent->statut_couleur);
    }

    public function test_presence_est_present(): void
    {
        $present = Presence::factory()->present()->create();
        $absent = Presence::factory()->absent()->create();

        $this->assertTrue($present->estPresent());
        $this->assertFalse($absent->estPresent());
    }

    public function test_presence_est_absent(): void
    {
        $present = Presence::factory()->present()->create();
        $absent = Presence::factory()->absent()->create();

        $this->assertFalse($present->estAbsent());
        $this->assertTrue($absent->estAbsent());
    }

    public function test_presence_marquer_present(): void
    {
        $presence = Presence::factory()->absent()->create();
        
        $presence->marquerPresent('Arrivé en retard');

        $this->assertTrue($presence->present);
        $this->assertEquals('Arrivé en retard', $presence->remarque);
    }

    public function test_presence_marquer_absent(): void
    {
        $presence = Presence::factory()->present()->create();
        
        $presence->marquerAbsent('Malade');

        $this->assertFalse($presence->present);
        $this->assertEquals('Malade', $presence->remarque);
    }

    public function test_presence_scope_presents(): void
    {
        Presence::factory()->present()->count(5)->create();
        Presence::factory()->absent()->count(3)->create();

        $this->assertEquals(5, Presence::presents()->count());
    }

    public function test_presence_scope_absents(): void
    {
        Presence::factory()->present()->count(5)->create();
        Presence::factory()->absent()->count(3)->create();

        $this->assertEquals(3, Presence::absents()->count());
    }

    public function test_presence_scope_pour_date(): void
    {
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        Presence::factory()->pourDate($today)->count(3)->create();
        Presence::factory()->pourDate($yesterday)->count(2)->create();

        $this->assertEquals(3, Presence::pourDate($today)->count());
    }

    public function test_presence_scope_pour_discipline(): void
    {
        $discipline1 = Discipline::factory()->create();
        $discipline2 = Discipline::factory()->create();

        Presence::factory()->forDiscipline($discipline1)->count(4)->create();
        Presence::factory()->forDiscipline($discipline2)->count(2)->create();

        $this->assertEquals(4, Presence::pourDiscipline($discipline1->id)->count());
    }

    public function test_presence_calculer_statistiques(): void
    {
        $discipline = Discipline::factory()->create();
        
        Presence::factory()->forDiscipline($discipline)->present()->count(7)->create();
        Presence::factory()->forDiscipline($discipline)->absent()->count(3)->create();

        $presences = Presence::pourDiscipline($discipline->id)->get();
        $stats = Presence::calculerStatistiques($presences);

        $this->assertEquals(10, $stats['total']);
        $this->assertEquals(7, $stats['presents']);
        $this->assertEquals(3, $stats['absents']);
        $this->assertEquals(70.0, $stats['taux']);
    }
}
