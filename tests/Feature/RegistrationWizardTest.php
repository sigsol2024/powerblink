<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Season;
use Database\Seeders\AcademyPermissionsSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(AcademyPermissionsSeeder::class);
    }

    public function test_wizard_step_one_persists_player_fields_in_session(): void
    {
        $season = Season::query()->create([
            'name' => '2026 Season',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);

        Program::query()->create([
            'season_id' => $season->id,
            'name' => 'U13 Development',
            'age_group' => 'U13',
            'registration_fee' => 3500000,
            'monthly_fee' => 6500000,
            'is_active' => true,
        ]);

        $this->post(route('registration.wizard.step'), [
            'step' => 1,
            'player_name' => 'Wizard Test Player',
            'date_of_birth' => '2013-05-01',
            'nationality' => 'Nigerian',
            'primary_position' => 'Midfielder',
        ])->assertRedirect(route('registration.wizard', ['step' => 2]));

        $this->get(route('registration.wizard', ['step' => 1]))
            ->assertOk()
            ->assertSee('value="Wizard Test Player"', false);
    }
}
