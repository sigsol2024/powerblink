<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Guardian;
use App\Models\InstallmentPlan;
use App\Models\AcademyPayment;
use App\Models\PerformanceReport;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\RegistrationPayment;
use App\Models\SessionAttendance;
use App\Models\Tournament;
use App\Models\TrainingSession;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ShowcaseSeedDensityTest extends TestCase
{
    use RefreshDatabase;

    public function test_showcase_seed_meets_minimum_row_counts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(10, Player::query()->count());
        $this->assertGreaterThanOrEqual(5, Guardian::query()->count());
        $this->assertGreaterThanOrEqual(10, TrainingSession::query()->count());
        $this->assertGreaterThanOrEqual(50, SessionAttendance::query()->count());
        $this->assertGreaterThanOrEqual(10, PerformanceReport::query()->count());
        $this->assertGreaterThanOrEqual(2, Tournament::query()->count());
        $this->assertGreaterThanOrEqual(5, Announcement::query()->count());
        $this->assertGreaterThanOrEqual(5, RegistrationPayment::query()->count() + AcademyPayment::query()->count());
        $this->assertGreaterThanOrEqual(5, InstallmentPlan::query()->count());
        $this->assertGreaterThanOrEqual(8, PlayerDocument::query()->count());
        $this->assertGreaterThanOrEqual(10, DB::table('notifications')->count());
    }
}
