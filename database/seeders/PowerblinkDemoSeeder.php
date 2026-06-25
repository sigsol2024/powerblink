<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\ExpandsShowcaseDemoData;
use App\Models\AcademyPayment;
use App\Models\Announcement;
use App\Models\Coach;
use App\Models\GalleryItem;
use App\Models\Guardian;
use App\Models\InstallmentPlan;
use App\Models\LeadershipMember;
use App\Models\Media;
use App\Models\PageSection;
use App\Models\PerformanceReport;
use App\Models\Player;
use App\Models\Program;
use App\Models\Registration;
use App\Models\RegistrationPayment;
use App\Models\Season;
use App\Models\SessionAttendance;
use App\Models\SiteTrafficEvent;
use App\Models\TimelineEvent;
use App\Models\Tournament;
use App\Models\TournamentSquad;
use App\Models\TrainingSession;
use App\Models\User;
use App\Models\CmsPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PowerblinkDemoSeeder extends Seeder
{
    use ExpandsShowcaseDemoData;

    public function run(): void
    {
        $bootstrapPassword = $this->resolveBootstrapPassword();

        $adminUser = $this->seedDemoUser(
            (string) config('powerblink.bootstrap_admin_name'),
            (string) config('powerblink.bootstrap_admin_email'),
            'admin',
            true,
            $bootstrapPassword,
        );
        $coachUser = $this->seedDemoUser('Coach Elijah Opetunde', 'coach@powerblinkfc.com', 'coach');
        $parentUser = $this->seedDemoUser('Adaeze Okonkwo', 'parent@powerblinkfc.com', 'parent');
        $playerUser = $this->seedDemoUser('Tobi Okonkwo', 'player@powerblinkfc.com', 'player');

        $season = Season::query()->updateOrCreate(
            ['name' => '2026 Season'],
            [
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_active' => true,
            ]
        );

        $programs = $this->seedPrograms($season);
        $coaches = $this->seedCoaches($coachUser);
        $guardians = $this->seedGuardians($parentUser);
        $guardian = $guardians['primary'];
        $secondGuardian = $guardians['second'];
        $registrations = $this->seedRegistrations($season, $programs, $guardian, $secondGuardian, $adminUser);
        $players = $this->seedPlayers($season, $programs, $guardian, $secondGuardian, $registrations, $playerUser);
        $players = $this->seedAdditionalPlayers($season, $programs, $guardians, $players);
        $this->seedRegistrationPayments($registrations, $players, $season);
        $this->seedExpandedAcademyPayments($players, $season);
        $this->seedExpandedInstallmentPlans($registrations, $players);
        $this->seedSiteTrafficEventsWithAcademyUrl();
        $sessions = $this->seedExpandedTrainingSessions($season, $programs, $coaches);
        $this->seedExpandedAttendance($sessions, $players);
        $this->seedExpandedPerformanceReports($season, $players, $coaches['head']);
        $this->seedTournaments($season, $players);
        $this->seedExpandedGallery();
        $this->seedExpandedAnnouncements($season, $adminUser);
        $this->seedPlayerDocuments($players, $registrations, $adminUser);
        $this->seedDemoNotifications($adminUser, $coachUser, $parentUser, $playerUser);
        $this->seedLeadership();
        $this->seedTimeline();
        $this->seedCmsPages();
        $this->seedPageSections();
        $this->seedFaqPageSections();
    }

    private function image(string $basename): string
    {
        return 'asset/images/powerblink/'.$basename;
    }

    private function seedDemoUser(
        string $name,
        string $email,
        string $role,
        bool $superAdmin = false,
        ?string $password = null,
    ): User {
        $password ??= $this->resolveDemoPassword();

        $factoryAttributes = User::factory()->make([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ])->getAttributes();
        $factoryAttributes['remember_token'] = null;

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            $factoryAttributes
        );

        if (! Hash::check($password, (string) $user->password)) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => null])->save();
        } elseif ($user->remember_token !== null) {
            $user->forceFill(['remember_token' => null])->save();
        }

        if ($superAdmin && ! $user->isSuperAdmin()) {
            $user->forceFill(['is_super_admin' => true])->save();
        }

        if (! $user->hasRole($role)) {
            $user->syncRoles([$role]);
        }

        return $user;
    }

    private function resolveBootstrapPassword(): string
    {
        $password = trim((string) config('powerblink.bootstrap_admin_password'));
        if ($password !== '') {
            return $password;
        }

        throw new \RuntimeException('Set BOOTSTRAP_ADMIN_PASSWORD in the environment before running PowerblinkDemoSeeder.');
    }

    private function resolveDemoPassword(): string
    {
        $password = trim((string) config('powerblink.demo_user_password'));
        if ($password !== '') {
            return $password;
        }

        throw new \RuntimeException('Set DEMO_USER_PASSWORD in the environment before running PowerblinkDemoSeeder.');
    }

    /**
     * @return array<string, Program>
     */
    private function seedPrograms(Season $season): array
    {
        $definitions = [
            'u7' => [
                'name' => 'U7 Grassroots',
                'age_group' => 'U7',
                'description' => 'Fun-first introduction to football for ages 5–7 with ball mastery and coordination.',
                'monthly_fee' => 4500000,
                'registration_fee' => 2500000,
                'max_capacity' => 24,
                'sessions_per_week' => 2,
                'hero_image' => 'programs-powerblink-fc-074.jpg',
                'sort_order' => 1,
            ],
            'u10' => [
                'name' => 'U10 Foundation',
                'age_group' => 'U10',
                'description' => 'Technical foundations, small-sided games, and disciplined training habits.',
                'monthly_fee' => 5500000,
                'registration_fee' => 3000000,
                'max_capacity' => 22,
                'sessions_per_week' => 3,
                'hero_image' => 'programs-powerblink-fc-075.jpg',
                'sort_order' => 2,
            ],
            'u13' => [
                'name' => 'U13 Development',
                'age_group' => 'U13',
                'description' => 'Tactical awareness, positional play, and competitive match preparation.',
                'monthly_fee' => 6500000,
                'registration_fee' => 3500000,
                'max_capacity' => 20,
                'sessions_per_week' => 3,
                'hero_image' => 'programs-powerblink-fc-076.jpg',
                'sort_order' => 3,
            ],
            'u15' => [
                'name' => 'U15 Elite',
                'age_group' => 'U15',
                'description' => 'High-performance pathway with strength conditioning, scouting exposure, and tournament play.',
                'monthly_fee' => 7500000,
                'registration_fee' => 4000000,
                'max_capacity' => 18,
                'sessions_per_week' => 4,
                'hero_image' => 'programs-powerblink-fc-077.jpg',
                'sort_order' => 4,
            ],
        ];

        $programs = [];
        foreach ($definitions as $key => $definition) {
            $programs[$key] = Program::query()->updateOrCreate(
                [
                    'season_id' => $season->id,
                    'age_group' => $definition['age_group'],
                ],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'monthly_fee' => $definition['monthly_fee'],
                    'registration_fee' => $definition['registration_fee'],
                    'max_capacity' => $definition['max_capacity'],
                    'sessions_per_week' => $definition['sessions_per_week'],
                    'is_active' => true,
                    'hero_image_media_id' => $this->mediaIdForPath($this->image($definition['hero_image'])),
                    'sort_order' => $definition['sort_order'],
                ]
            );
        }

        return $programs;
    }

    /**
     * @return array<string, Coach>
     */
    private function seedCoaches(User $coachUser): array
    {
        $definitions = [
            'head' => [
                'name' => 'Coach Elijah Opetunde',
                'title' => 'Head Coach',
                'specialization' => 'Youth Development',
                'bio' => 'Former professional midfielder with 18 years coaching elite academy squads across West Africa.',
                'certifications' => ['UEFA B License', 'CAF C License', 'Safeguarding Level 2'],
                'experience_years' => 18,
                'license_level' => 'UEFA B',
                'email' => 'coach@powerblinkfc.com',
                'phone' => '+234 801 234 5678',
                'photo' => 'coaching-team-powerblink-fc-025.jpg',
                'user_id' => $coachUser->id,
                'sort_order' => 1,
            ],
            'technical' => [
                'name' => 'Coach Amara Nwosu',
                'title' => 'Technical Director',
                'specialization' => 'Tactical Systems',
                'bio' => 'Architect of Powerblink FC curriculum with a focus on positional intelligence and match analysis.',
                'certifications' => ['UEFA A License', 'FA Level 3', 'Sports Analytics Certificate'],
                'experience_years' => 14,
                'license_level' => 'UEFA A',
                'email' => 'amara.nwosu@powerblinkfc.com',
                'phone' => '+234 802 345 6789',
                'photo' => 'coaching-team-management-powerblink-fc-020.jpg',
                'sort_order' => 2,
            ],
            'goalkeeper' => [
                'name' => 'Coach Kunle Adeyemi',
                'title' => 'Goalkeeper Coach',
                'specialization' => 'Shot Stopping',
                'bio' => 'Specialist goalkeeper coach developing reflexes, distribution, and command of area.',
                'certifications' => ['GK Level 2', 'CAF C License', 'First Aid Certified'],
                'experience_years' => 9,
                'license_level' => 'GK Level 2',
                'email' => 'kunle.adeyemi@powerblinkfc.com',
                'phone' => '+234 803 456 7890',
                'photo' => 'coaching-team-management-powerblink-fc-021.jpg',
                'sort_order' => 3,
            ],
            'fitness' => [
                'name' => 'Coach Priya Mensah',
                'title' => 'Sports Science Lead',
                'specialization' => 'Conditioning',
                'bio' => 'Leads academy fitness testing, load management, and recovery protocols.',
                'certifications' => ['MSc Sports Science', 'Strength & Conditioning Level 1', 'Nutrition Basics'],
                'experience_years' => 11,
                'license_level' => 'S&C Level 1',
                'email' => 'priya.mensah@powerblinkfc.com',
                'phone' => '+234 804 567 8901',
                'photo' => 'coaching-team-management-powerblink-fc-022.jpg',
                'sort_order' => 4,
            ],
        ];

        $coaches = [];
        foreach ($definitions as $key => $definition) {
            $coaches[$key] = Coach::query()->updateOrCreate(
                ['email' => $definition['email']],
                [
                    'user_id' => $definition['user_id'] ?? null,
                    'name' => $definition['name'],
                    'title' => $definition['title'],
                    'bio' => $definition['bio'],
                    'specialization' => $definition['specialization'],
                    'certifications' => $definition['certifications'],
                    'experience_years' => $definition['experience_years'],
                    'license_level' => $definition['license_level'],
                    'phone' => $definition['phone'],
                    'photo_media_id' => $this->mediaIdForPath($this->image($definition['photo'])),
                    'is_active' => true,
                    'sort_order' => $definition['sort_order'],
                ]
            );
        }

        return $coaches;
    }

    private function seedGuardian(User $parentUser): Guardian
    {
        return $this->seedGuardians($parentUser)['primary'];
    }

    /**
     * @param  array<string, Program>  $programs
     * @return array<string, Registration>
     */
    private function seedRegistrations(
        Season $season,
        array $programs,
        Guardian $guardian,
        Guardian $secondGuardian,
        User $adminUser,
    ): array {
        $definitions = [
            'activated' => [
                'reference_code' => 'PB-REG-2026-001',
                'program' => $programs['u13'],
                'guardian' => $guardian,
                'status' => 'activated',
                'player_name' => 'Tobi Okonkwo',
                'date_of_birth' => '2013-04-18',
                'nationality' => 'Nigerian',
                'primary_position' => 'Midfielder',
                'secondary_position' => 'Winger',
                'years_experience' => 4,
                'technical_strengths' => 'Vision, first touch, work rate',
                'photo' => 'admin-dashboard-powerblink-fc-011.jpg',
                'payment_plan' => 'lump_sum',
                'submitted_at' => Carbon::parse('2026-01-10 09:30:00'),
                'approved_at' => Carbon::parse('2026-01-12 14:00:00'),
            ],
            'pending' => [
                'reference_code' => 'PB-REG-2026-002',
                'program' => $programs['u10'],
                'guardian' => $guardian,
                'status' => 'pending_review',
                'player_name' => 'Kemi Adebayo',
                'date_of_birth' => '2016-08-02',
                'nationality' => 'Nigerian',
                'primary_position' => 'Forward',
                'secondary_position' => 'Attacking Midfielder',
                'years_experience' => 2,
                'technical_strengths' => 'Pace, finishing, confidence on the ball',
                'photo' => 'registrations-powerblink-fc-080.jpg',
                'payment_plan' => 'lump_sum',
                'submitted_at' => Carbon::parse('2026-06-20 11:15:00'),
            ],
            'awaiting_payment' => [
                'reference_code' => 'PB-REG-2026-003',
                'program' => $programs['u15'],
                'guardian' => $secondGuardian,
                'status' => 'awaiting_payment',
                'player_name' => 'Daniel Eze',
                'date_of_birth' => '2011-11-05',
                'nationality' => 'Nigerian',
                'primary_position' => 'Defender',
                'secondary_position' => 'Centre Back',
                'years_experience' => 5,
                'technical_strengths' => 'Tackling, aerial ability, leadership',
                'photo' => 'performance-analytics-powerblink-fc-055.jpg',
                'payment_plan' => 'installments',
                'payment_token' => (string) Str::uuid(),
                'payment_token_expires_at' => Carbon::parse('2026-07-15 23:59:59'),
                'submitted_at' => Carbon::parse('2026-06-18 16:40:00'),
                'approved_at' => Carbon::parse('2026-06-19 10:00:00'),
            ],
        ];

        $registrations = [];
        foreach ($definitions as $key => $definition) {
            $registrations[$key] = Registration::query()->updateOrCreate(
                ['reference_code' => $definition['reference_code']],
                [
                    'season_id' => $season->id,
                    'program_id' => $definition['program']->id,
                    'guardian_id' => $definition['guardian']->id,
                    'status' => $definition['status'],
                    'payment_plan' => $definition['payment_plan'],
                    'payment_token' => $definition['payment_token'] ?? null,
                    'payment_token_expires_at' => $definition['payment_token_expires_at'] ?? null,
                    'player_name' => $definition['player_name'],
                    'date_of_birth' => $definition['date_of_birth'],
                    'nationality' => $definition['nationality'],
                    'primary_position' => $definition['primary_position'],
                    'secondary_position' => $definition['secondary_position'],
                    'years_experience' => $definition['years_experience'],
                    'technical_strengths' => $definition['technical_strengths'],
                    'fitness_certified' => true,
                    'profile_photo_media_id' => $this->mediaIdForPath($this->image($definition['photo'])),
                    'emergency_contact_name' => $definition['guardian']->emergency_contact_name,
                    'emergency_contact_phone' => $definition['guardian']->emergency_contact_phone,
                    'emergency_contact_relationship' => $definition['guardian']->emergency_contact_relationship,
                    'approved_by' => isset($definition['approved_at']) ? $adminUser->id : null,
                    'approved_at' => $definition['approved_at'] ?? null,
                    'submitted_at' => $definition['submitted_at'],
                ]
            );
        }

        return $registrations;
    }

    /**
     * @param  array<string, Program>  $programs
     * @param  array<string, Registration>  $registrations
     * @return array<string, Player>
     */
    private function seedPlayers(
        Season $season,
        array $programs,
        Guardian $guardian,
        Guardian $secondGuardian,
        array $registrations,
        User $playerUser
    ): array {
        $definitions = [
            'demo' => [
                'player_code' => 'PB-PLY-2026-001',
                'registration' => $registrations['activated'],
                'program' => $programs['u13'],
                'guardian' => $guardian,
                'user_id' => $playerUser->id,
                'name' => 'Tobi Okonkwo',
                'date_of_birth' => '2013-04-18',
                'primary_position' => 'Midfielder',
                'photo' => 'player-dashboard-powerblink-fc-060.jpg',
            ],
            'squad' => [
                'player_code' => 'PB-PLY-2026-002',
                'registration' => null,
                'program' => $programs['u15'],
                'guardian' => $secondGuardian,
                'user_id' => null,
                'name' => 'Daniel Eze',
                'date_of_birth' => '2011-11-05',
                'primary_position' => 'Defender',
                'photo' => 'performance-analytics-powerblink-fc-055.jpg',
            ],
            'squad2' => [
                'player_code' => 'PB-PLY-2026-003',
                'registration' => null,
                'program' => $programs['u10'],
                'guardian' => $guardian,
                'user_id' => null,
                'name' => 'Zainab Bello',
                'date_of_birth' => '2016-03-22',
                'primary_position' => 'Goalkeeper',
                'photo' => 'attendance-tracking-powerblink-fc-015.jpg',
            ],
            'squad3' => [
                'player_code' => 'PB-PLY-2026-004',
                'registration' => null,
                'program' => $programs['u7'],
                'guardian' => $guardian,
                'user_id' => null,
                'name' => 'Emeka Nwachukwu',
                'date_of_birth' => '2019-05-14',
                'primary_position' => 'Forward',
                'photo' => 'home-powerblink-fc-046.jpg',
            ],
        ];

        $players = [];
        foreach ($definitions as $key => $definition) {
            $players[$key] = Player::query()->updateOrCreate(
                ['player_code' => $definition['player_code']],
                [
                    'registration_id' => $definition['registration']?->id,
                    'user_id' => $definition['user_id'],
                    'guardian_id' => $definition['guardian']->id,
                    'program_id' => $definition['program']->id,
                    'season_id' => $season->id,
                    'photo_media_id' => $this->mediaIdForPath($this->image($definition['photo'])),
                    'name' => $definition['name'],
                    'date_of_birth' => $definition['date_of_birth'],
                    'nationality' => 'Nigerian',
                    'primary_position' => $definition['primary_position'],
                    'years_experience' => 3,
                    'technical_strengths' => 'Committed, coachable, team-oriented',
                    'status' => 'active',
                ]
            );
        }

        return $players;
    }

    /**
     * @param  array<string, Registration>  $registrations
     * @param  array<string, Player>  $players
     */
    private function seedRegistrationPayments(array $registrations, array $players, Season $season): void
    {
        RegistrationPayment::query()->updateOrCreate(
            ['reference' => 'PB-PAY-REG-2026-001'],
            [
                'registration_id' => $registrations['activated']->id,
                'player_id' => $players['demo']->id,
                'season_id' => $season->id,
                'type' => 'registration_fee',
                'provider' => 'paystack',
                'status' => 'success',
                'amount' => 3500000,
                'currency' => 'NGN',
                'paid_at' => Carbon::parse('2026-01-13 10:45:00'),
            ]
        );

        RegistrationPayment::query()->updateOrCreate(
            ['reference' => 'PB-PAY-REG-2026-PENDING'],
            [
                'registration_id' => $registrations['awaiting_payment']->id,
                'season_id' => $season->id,
                'type' => 'registration_fee',
                'provider' => 'paystack',
                'status' => 'pending',
                'amount' => 4000000,
                'currency' => 'NGN',
            ]
        );
    }

    /**
     * @param  array<string, Player>  $players
     */
    private function seedAcademyPayments(array $players, Season $season): void
    {
        $definitions = [
            [
                'reference' => 'PB-PAY-MONTHLY-001',
                'player' => $players['demo'],
                'type' => 'monthly_fee',
                'status' => 'success',
                'amount' => 6500000,
                'paid_at' => Carbon::parse('2026-06-01 09:00:00'),
            ],
            [
                'reference' => 'PB-PAY-MONTHLY-002',
                'player' => $players['squad'],
                'type' => 'monthly_fee',
                'status' => 'success',
                'amount' => 7500000,
                'paid_at' => Carbon::parse('2026-06-01 09:15:00'),
            ],
            [
                'reference' => 'PB-PAY-MONTHLY-003',
                'player' => $players['squad2'],
                'type' => 'monthly_fee',
                'status' => 'pending',
                'amount' => 5500000,
                'paid_at' => null,
            ],
        ];

        foreach ($definitions as $definition) {
            AcademyPayment::query()->updateOrCreate(
                ['reference' => $definition['reference']],
                [
                    'player_id' => $definition['player']->id,
                    'season_id' => $season->id,
                    'type' => $definition['type'],
                    'provider' => 'paystack',
                    'status' => $definition['status'],
                    'amount' => $definition['amount'],
                    'currency' => 'NGN',
                    'paid_at' => $definition['paid_at'],
                ]
            );
        }
    }

    /**
     * @param  array<string, Registration>  $registrations
     * @param  array<string, Player>  $players
     */
    private function seedInstallmentPlans(array $registrations, array $players): void
    {
        $installments = [
            [
                'registration_id' => $registrations['awaiting_payment']->id,
                'player_id' => null,
                'amount' => 1333333,
                'due_date' => '2026-07-01',
                'status' => 'pending',
            ],
            [
                'registration_id' => $registrations['awaiting_payment']->id,
                'player_id' => null,
                'amount' => 1333333,
                'due_date' => '2026-08-01',
                'status' => 'pending',
            ],
            [
                'registration_id' => $registrations['awaiting_payment']->id,
                'player_id' => null,
                'amount' => 1333334,
                'due_date' => '2026-09-01',
                'status' => 'pending',
            ],
            [
                'registration_id' => $registrations['activated']->id,
                'player_id' => $players['demo']->id,
                'amount' => 3500000,
                'due_date' => '2026-01-13',
                'status' => 'paid',
            ],
        ];

        foreach ($installments as $index => $installment) {
            InstallmentPlan::query()->updateOrCreate(
                [
                    'registration_id' => $installment['registration_id'],
                    'due_date' => $installment['due_date'],
                ],
                [
                    'player_id' => $installment['player_id'],
                    'amount' => $installment['amount'],
                    'status' => $installment['status'],
                ]
            );
        }
    }

    private function seedSiteTrafficEvents(): void
    {
        $paths = [
            ['path' => '/', 'route_name' => 'home', 'views' => 42],
            ['path' => '/programs', 'route_name' => 'programs', 'views' => 28],
            ['path' => '/about', 'route_name' => 'about', 'views' => 19],
            ['path' => '/register', 'route_name' => 'registration.wizard', 'views' => 35],
            ['path' => '/gallery', 'route_name' => 'gallery', 'views' => 15],
            ['path' => '/contact', 'route_name' => 'contact', 'views' => 12],
        ];

        $sessionBase = 'demo-session-';
        $day = now()->subDays(30);

        foreach ($paths as $pathDef) {
            for ($i = 0; $i < $pathDef['views']; $i++) {
                SiteTrafficEvent::query()->create([
                    'path' => $pathDef['path'],
                    'route_name' => $pathDef['route_name'],
                    'url' => url($pathDef['path']),
                    'method' => 'GET',
                    'referrer_host' => $i % 3 === 0 ? 'google.com' : ($i % 3 === 1 ? 'instagram.com' : null),
                    'session_id' => $sessionBase.($i % 12),
                    'viewed_at' => $day->copy()->addDays($i % 28)->addHours($i % 8),
                ]);
            }
        }
    }

    /**
     * @param  array<string, Program>  $programs
     * @param  array<string, Coach>  $coaches
     * @return list<TrainingSession>
     */
    private function seedTrainingSessions(Season $season, array $programs, array $coaches): array
    {
        $definitions = [
            [
                'program' => $programs['u13'],
                'coach' => $coaches['head'],
                'title' => 'U13 Technical Session',
                'session_type' => 'technical',
                'date' => '2026-06-21',
                'start_time' => '16:00:00',
                'end_time' => '17:30:00',
                'location' => 'Pitch A, Powerblink Academy',
            ],
            [
                'program' => $programs['u15'],
                'coach' => $coaches['technical'],
                'title' => 'U15 Tactical Block',
                'session_type' => 'tactical',
                'date' => '2026-06-22',
                'start_time' => '17:00:00',
                'end_time' => '18:30:00',
                'location' => 'Pitch B, Powerblink Academy',
            ],
            [
                'program' => $programs['u10'],
                'coach' => $coaches['goalkeeper'],
                'title' => 'U10 Small-Sided Games',
                'session_type' => 'match_play',
                'date' => '2026-06-23',
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'location' => 'Pitch C, Powerblink Academy',
            ],
        ];

        $sessions = [];
        foreach ($definitions as $definition) {
            $sessions[] = TrainingSession::query()->updateOrCreate(
                [
                    'season_id' => $season->id,
                    'program_id' => $definition['program']->id,
                    'title' => $definition['title'],
                    'date' => $definition['date'],
                ],
                [
                    'coach_id' => $definition['coach']->id,
                    'session_type' => $definition['session_type'],
                    'start_time' => $definition['start_time'],
                    'end_time' => $definition['end_time'],
                    'location' => $definition['location'],
                    'notes' => 'Demo training session seeded for Powerblink FC.',
                ]
            );
        }

        return $sessions;
    }

    /**
     * @param  list<TrainingSession>  $sessions
     * @param  array<string, Player>  $players
     */
    private function seedAttendance(array $sessions, array $players): void
    {
        $statuses = [
            $players['demo']->id => 'present',
            $players['squad']->id => 'present',
            $players['squad2']->id => 'absent',
            $players['squad3']->id => 'present',
        ];

        foreach ($sessions as $session) {
            foreach ($statuses as $playerId => $status) {
                SessionAttendance::query()->updateOrCreate(
                    [
                        'training_session_id' => $session->id,
                        'player_id' => $playerId,
                    ],
                    [
                        'status' => $status,
                        'remarks' => $status === 'absent' ? 'Reported illness' : null,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, Player>  $players
     */
    private function seedPerformanceReports(Season $season, array $players, Coach $coach): void
    {
        foreach ([$players['demo'], $players['squad']] as $player) {
            PerformanceReport::query()->updateOrCreate(
                [
                    'season_id' => $season->id,
                    'player_id' => $player->id,
                    'coach_id' => $coach->id,
                    'reported_at' => Carbon::parse('2026-06-15 12:00:00'),
                ],
                [
                    'passing' => 82,
                    'dribbling' => 78,
                    'speed' => 80,
                    'fitness' => 85,
                    'discipline' => 88,
                    'teamwork' => 90,
                    'overall_score' => 83.8,
                    'comments' => 'Strong progress this cycle. Continue focusing on decision-making under pressure.',
                ]
            );
        }
    }

    /**
     * @param  array<string, Player>  $players
     */
    private function seedTournament(Season $season, array $players): Tournament
    {
        $tournament = Tournament::query()->updateOrCreate(
            [
                'season_id' => $season->id,
                'title' => 'Powerblink Independence Day Tournament',
            ],
            [
                'category' => 'U13-U15',
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-03',
                'location' => 'Powerblink Academy Grounds, Ibeju Lekki',
                'description' => 'Annual showcase tournament featuring academy squads and invited youth clubs.',
                'status' => 'upcoming',
                'max_teams' => 12,
                'featured_image_media_id' => $this->mediaIdForPath($this->image('home-powerblink-fc-054.jpg')),
            ]
        );

        foreach ([$players['demo'], $players['squad']] as $player) {
            TournamentSquad::query()->updateOrCreate(
                [
                    'tournament_id' => $tournament->id,
                    'player_id' => $player->id,
                ],
                [
                    'position' => $player->primary_position,
                ]
            );
        }

        return $tournament;
    }

    private function seedGallery(): void
    {
        $items = [
            ['title' => 'Golden Hour Training', 'category' => 'training', 'image' => 'home-powerblink-fc-044.jpg', 'sort_order' => 1],
            ['title' => 'U7 Grassroots Joy', 'category' => 'programs', 'image' => 'home-powerblink-fc-046.jpg', 'sort_order' => 2],
            ['title' => 'Tactical Briefing', 'category' => 'coaching', 'image' => 'home-powerblink-fc-045.jpg', 'sort_order' => 3],
            ['title' => 'Tournament Action', 'category' => 'tournaments', 'image' => 'home-powerblink-fc-054.jpg', 'sort_order' => 4],
            ['title' => 'Academy Facilities', 'category' => 'facilities', 'image' => 'about-us-powerblink-fc-001.jpg', 'sort_order' => 5],
        ];

        foreach ($items as $item) {
            $mediaId = $this->mediaIdForPath($this->image($item['image']));
            if ($mediaId === null) {
                continue;
            }

            GalleryItem::query()->updateOrCreate(
                [
                    'media_id' => $mediaId,
                    'title' => $item['title'],
                ],
                [
                    'category' => $item['category'],
                    'sort_order' => $item['sort_order'],
                    'is_published' => true,
                ]
            );
        }
    }

    private function seedAnnouncements(Season $season, User $adminUser): void
    {
        $announcements = [
            [
                'title' => '2026 Season Registration Now Open',
                'body' => 'Applications are open for U7 through U15 programs. Submit your registration and our team will review within 48 hours.',
                'audience' => 'parents',
                'published_at' => Carbon::parse('2026-01-05 08:00:00'),
            ],
            [
                'title' => 'Independence Day Tournament Squad Selection',
                'body' => 'Coaches will announce preliminary tournament squads after the June assessment window.',
                'audience' => 'all',
                'published_at' => Carbon::parse('2026-06-10 09:00:00'),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::query()->updateOrCreate(
                [
                    'season_id' => $season->id,
                    'title' => $announcement['title'],
                ],
                [
                    'body' => $announcement['body'],
                    'audience' => $announcement['audience'],
                    'channel' => 'in_app',
                    'published_at' => $announcement['published_at'],
                    'created_by' => $adminUser->id,
                ]
            );
        }
    }

    private function seedLeadership(): void
    {
        $members = [
            [
                'name' => 'Elijah Opetunde',
                'title' => 'Director of Football',
                'bio' => 'Leads the football philosophy and long-term player pathway at Powerblink FC.',
                'photo' => 'coaching-team-powerblink-fc-025.jpg',
                'sort_order' => 1,
            ],
            [
                'name' => 'Ngozi Ekeh',
                'title' => 'Academy Chair',
                'bio' => 'Oversees governance, partnerships, and community engagement for the academy.',
                'photo' => 'coaching-team-powerblink-fc-027.jpg',
                'sort_order' => 2,
            ],
        ];

        foreach ($members as $member) {
            LeadershipMember::query()->updateOrCreate(
                ['name' => $member['name']],
                [
                    'title' => $member['title'],
                    'bio' => $member['bio'],
                    'photo_media_id' => $this->mediaIdForPath($this->image($member['photo'])),
                    'sort_order' => $member['sort_order'],
                ]
            );
        }
    }

    private function seedTimeline(): void
    {
        $events = [
            ['year' => 2018, 'title' => 'Academy Founded', 'description' => 'Powerblink FC launches grassroots training in Ibeju Lekki.', 'sort_order' => 1],
            ['year' => 2021, 'title' => 'Elite Performance Framework', 'description' => 'Structured U7–U15 pathways and sports science integration introduced.', 'sort_order' => 2],
            ['year' => 2024, 'title' => 'Independence Day Tournament', 'description' => 'Inaugural academy-wide tournament draws regional youth clubs.', 'sort_order' => 3],
            ['year' => 2026, 'title' => 'Digital Academy Platform', 'description' => 'Parent, player, and coach portals launch for registrations, attendance, and payments.', 'sort_order' => 4],
        ];

        foreach ($events as $event) {
            TimelineEvent::query()->updateOrCreate(
                [
                    'year' => $event['year'],
                    'title' => $event['title'],
                ],
                [
                    'description' => $event['description'],
                    'sort_order' => $event['sort_order'],
                ]
            );
        }
    }

    private function seedCmsPages(): void
    {
        $pages = [
            'home' => [
                'title' => 'Home',
                'meta_description' => 'Powerblink FC — Elite football academy in Ibeju Lekki developing tomorrow\'s stars.',
            ],
            'about' => [
                'title' => 'About Powerblink FC',
                'meta_description' => 'Learn about our mission, coaching philosophy, and elite academy facilities.',
            ],
            'contact' => [
                'title' => 'Contact Us',
                'meta_description' => 'Reach Powerblink FC for registrations, tours, and academy enquiries.',
            ],
            'faq' => [
                'title' => 'FAQ',
                'meta_description' => 'Frequently asked questions about programs, fees, and registration.',
            ],
        ];

        foreach ($pages as $slug => $page) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $page['title'],
                    'meta_description' => $page['meta_description'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedPageSections(): void
    {
        $sections = [
            'home' => [
                ['section_key' => 'hero_title', 'content_type' => 'text', 'content' => 'Developing Tomorrow\'s Football Stars Today'],
                ['section_key' => 'hero_subtitle', 'content_type' => 'text', 'content' => 'Elite youth development in Ibeju Lekki with world-class coaching and structured pathways from U7 to U15.'],
                ['section_key' => 'hero_cta_text', 'content_type' => 'text', 'content' => 'Register Now'],
                ['section_key' => 'hero_cta_href', 'content_type' => 'text', 'content' => '/register'],
                ['section_key' => 'home_search_label', 'content_type' => 'text', 'content' => 'Explore Powerblink FC programs'],
                ['section_key' => 'recent_title', 'content_type' => 'text', 'content' => 'Why Our Academy Stands Out'],
                ['section_key' => 'recent_subtitle', 'content_type' => 'textarea', 'content' => 'The Powerblink Edge combines elite coaching, modern facilities, and a player-first culture that develops disciplined leaders on and off the pitch.'],
                ['section_key' => 'hero_image', 'content_type' => 'image', 'content' => 'asset/images/powerblink/home-powerblink-fc-044.jpg'],
                ['section_key' => 'cta_left_image', 'content_type' => 'image', 'content' => 'asset/images/powerblink/home-powerblink-fc-045.jpg'],
                ['section_key' => 'cta_right_image', 'content_type' => 'image', 'content' => 'asset/images/powerblink/home-powerblink-fc-046.jpg'],
                ['section_key' => 'cta_left_title', 'content_type' => 'text', 'content' => 'Elite Excellence in Ibeju Lekki'],
                ['section_key' => 'cta_left_body', 'content_type' => 'textarea', 'content' => 'Powerblink Football Club Limited provides a safe, world-class environment where young athletes transform raw passion into professional competence.'],
                ['section_key' => 'cta_right_title', 'content_type' => 'text', 'content' => 'Programs for Every Stage'],
                ['section_key' => 'cta_right_body', 'content_type' => 'textarea', 'content' => 'From U7 grassroots joy to U15 elite competition, every pathway is designed with technical mastery, tactical intelligence, and character development.'],
                ['section_key' => 'feat1_title', 'content_type' => 'text', 'content' => 'Licensed Coaching Staff'],
                ['section_key' => 'feat1_body', 'content_type' => 'textarea', 'content' => 'UEFA- and CAF-certified coaches deliver structured sessions backed by performance analytics.'],
                ['section_key' => 'feat2_title', 'content_type' => 'text', 'content' => 'Modern Training Facilities'],
                ['section_key' => 'feat2_body', 'content_type' => 'textarea', 'content' => 'Premium pitches, recovery spaces, and sports science support in the heart of Ibeju Lekki.'],
                ['section_key' => 'feat3_title', 'content_type' => 'text', 'content' => 'Clear Player Pathway'],
                ['section_key' => 'feat3_body', 'content_type' => 'textarea', 'content' => 'Transparent progression from grassroots to elite squads with tournament and scouting exposure.'],
                ['section_key' => 'welcome_title', 'content_type' => 'text', 'content' => 'Welcome to Powerblink FC'],
                ['section_key' => 'welcome_body', 'content_type' => 'textarea', 'content' => 'We develop the person first and the player second — raising disciplined leaders who happen to be incredible footballers.'],
                ['section_key' => 'prefooter_title', 'content_type' => 'text', 'content' => 'Ready to join the academy?'],
                ['section_key' => 'prefooter_button_text', 'content_type' => 'text', 'content' => 'Start Registration'],
                ['section_key' => 'prefooter_button_href', 'content_type' => 'text', 'content' => '/register'],
                ['section_key' => 'testimonial_name', 'content_type' => 'text', 'content' => 'Coach Elijah Opetunde'],
                ['section_key' => 'testimonial_role', 'content_type' => 'text', 'content' => 'Head Coach, Powerblink FC'],
                ['section_key' => 'testimonial_quote', 'content_type' => 'textarea', 'content' => 'Develop the person first, the player second. Our goal at Powerblink is to raise disciplined leaders who happen to be incredible footballers.'],
            ],
            'contact' => [
                ['section_key' => 'heading', 'content_type' => 'text', 'content' => 'Contact Powerblink FC'],
                ['section_key' => 'intro', 'content_type' => 'textarea', 'content' => 'Visit our academy in Ibeju Lekki or reach out for registration support, facility tours, and partnership enquiries.'],
                ['section_key' => 'hero_image', 'content_type' => 'image', 'content' => 'asset/images/powerblink/contact-us-powerblink-fc-033.jpg'],
                ['section_key' => 'map_image', 'content_type' => 'image', 'content' => 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg'],
            ],
        ];

        foreach ($sections as $page => $items) {
            foreach ($items as $item) {
                PageSection::query()->updateOrCreate(
                    [
                        'page' => $page,
                        'section_key' => $item['section_key'],
                    ],
                    [
                        'content_type' => $item['content_type'],
                        'content' => $item['content'],
                    ]
                );
            }
        }
    }

    private function mediaIdForPath(string $path): ?int
    {
        $filename = basename($path);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $media = Media::query()->firstOrCreate(
            ['file_path' => $path],
            [
                'filename' => $filename,
                'original_name' => $filename,
                'file_type' => $extension !== '' ? $extension : 'jpg',
                'file_size' => 0,
                'category' => 'powerblink',
            ]
        );

        return $media->id;
    }
}
