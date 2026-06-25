<?php

namespace Database\Seeders\Concerns;

use App\Models\AcademyPayment;
use App\Models\Announcement;
use App\Models\Coach;
use App\Models\GalleryItem;
use App\Models\Guardian;
use App\Models\InstallmentPlan;
use App\Models\PageSection;
use App\Models\PerformanceReport;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\Program;
use App\Models\Registration;
use App\Models\RegistrationPayment;
use App\Models\Season;
use App\Models\SessionAttendance;
use App\Models\SiteTrafficEvent;
use App\Models\Tournament;
use App\Models\TournamentSquad;
use App\Models\TrainingSession;
use App\Models\User;
use App\Notifications\AcademyAlert;
use Illuminate\Support\Carbon;

trait ExpandsShowcaseDemoData
{
    /**
     * @return array{primary: Guardian, second: Guardian, extra: list<Guardian>}
     */
    private function seedGuardians(User $parentUser): array
    {
        $primary = Guardian::query()->updateOrCreate(
            ['email' => 'parent@powerblinkfc.com'],
            [
                'user_id' => $parentUser->id,
                'name' => 'Adaeze Okonkwo',
                'phone' => '+234 805 678 9012',
                'address' => '12 Admiralty Way, Lekki Phase 1, Lagos',
                'relationship' => 'Mother',
                'emergency_contact_name' => 'Chidi Okonkwo',
                'emergency_contact_phone' => '+234 806 789 0123',
                'emergency_contact_relationship' => 'Father',
            ]
        );

        $second = Guardian::query()->updateOrCreate(
            ['email' => 'chidi.okonkwo@example.com'],
            [
                'name' => 'Chidi Okonkwo',
                'phone' => '+234 807 890 1234',
                'address' => '45 Victoria Island, Lagos',
                'relationship' => 'Father',
                'emergency_contact_name' => 'Adaeze Okonkwo',
                'emergency_contact_phone' => '+234 805 678 9012',
                'emergency_contact_relationship' => 'Mother',
            ]
        );

        $extraDefinitions = [
            ['email' => 'fatima.yusuf@example.com', 'name' => 'Fatima Yusuf', 'phone' => '+234 808 111 2233', 'address' => '8 Chevron Drive, Lekki', 'relationship' => 'Mother'],
            ['email' => 'james.okafor@example.com', 'name' => 'James Okafor', 'phone' => '+234 809 222 3344', 'address' => '22 Ajah Road, Lagos', 'relationship' => 'Father'],
            ['email' => 'blessing.nnamdi@example.com', 'name' => 'Blessing Nnamdi', 'phone' => '+234 810 333 4455', 'address' => '5 Sangotedo, Lagos', 'relationship' => 'Guardian'],
            ['email' => 'henry.davies@example.com', 'name' => 'Henry Davies', 'phone' => '+234 811 444 5566', 'address' => '14 Ikoyi, Lagos', 'relationship' => 'Father'],
        ];

        $extra = [];
        foreach ($extraDefinitions as $definition) {
            $extra[] = Guardian::query()->updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'phone' => $definition['phone'],
                    'address' => $definition['address'],
                    'relationship' => $definition['relationship'],
                    'emergency_contact_name' => 'Academy Office',
                    'emergency_contact_phone' => '+234 700 000 0000',
                    'emergency_contact_relationship' => 'Staff',
                ]
            );
        }

        return ['primary' => $primary, 'second' => $second, 'extra' => $extra];
    }

    /**
     * @param  array<string, Program>  $programs
     * @param  array{primary: Guardian, second: Guardian, extra: list<Guardian>}  $guardians
     * @param  array<string, Player>  $players
     * @return array<string, Player>
     */
    private function seedAdditionalPlayers(Season $season, array $programs, array $guardians, array $players): array
    {
        $allGuardians = array_merge([$guardians['primary'], $guardians['second']], $guardians['extra']);
        $photos = [
            'player-management-powerblink-fc-061.jpg',
            'player-management-powerblink-fc-062.jpg',
            'player-management-powerblink-fc-063.jpg',
            'player-management-powerblink-fc-064.jpg',
            'player-management-powerblink-fc-065.jpg',
            'player-dashboard-powerblink-fc-061.jpg',
            'player-dashboard-powerblink-fc-062.jpg',
            'player-dashboard-powerblink-fc-063.jpg',
        ];

        $definitions = [
            ['name' => 'Amina Hassan', 'program' => 'u7', 'position' => 'Midfielder', 'dob' => '2019-02-10'],
            ['name' => 'David Chukwu', 'program' => 'u7', 'position' => 'Forward', 'dob' => '2018-11-28'],
            ['name' => 'Grace Etim', 'program' => 'u10', 'position' => 'Winger', 'dob' => '2016-07-14'],
            ['name' => 'Ibrahim Musa', 'program' => 'u10', 'position' => 'Defender', 'dob' => '2016-01-05'],
            ['name' => 'Lara Adekunle', 'program' => 'u13', 'position' => 'Midfielder', 'dob' => '2013-09-20'],
            ['name' => 'Michael Obi', 'program' => 'u13', 'position' => 'Forward', 'dob' => '2013-12-01'],
            ['name' => 'Ngozi Akpan', 'program' => 'u15', 'position' => 'Goalkeeper', 'dob' => '2011-06-18'],
            ['name' => 'Samuel Adeyemi', 'program' => 'u15', 'position' => 'Defender', 'dob' => '2011-03-09'],
        ];

        foreach ($definitions as $index => $definition) {
            $code = sprintf('PB-PLY-2026-%03d', $index + 5);
            $guardian = $allGuardians[$index % count($allGuardians)];
            $photo = $photos[$index % count($photos)];

            $players['extra_'.$index] = Player::query()->updateOrCreate(
                ['player_code' => $code],
                [
                    'registration_id' => null,
                    'user_id' => null,
                    'guardian_id' => $guardian->id,
                    'program_id' => $programs[$definition['program']]->id,
                    'season_id' => $season->id,
                    'photo_media_id' => $this->mediaIdForPath($this->image($photo)),
                    'name' => $definition['name'],
                    'date_of_birth' => $definition['dob'],
                    'nationality' => 'Nigerian',
                    'primary_position' => $definition['position'],
                    'years_experience' => 2 + ($index % 4),
                    'technical_strengths' => 'Committed, coachable, team-oriented',
                    'status' => 'active',
                ]
            );
        }

        return $players;
    }

    /**
     * @param  array<string, Program>  $programs
     * @param  array<string, Coach>  $coaches
     * @return list<TrainingSession>
     */
    private function seedExpandedTrainingSessions(Season $season, array $programs, array $coaches): array
    {
        $programKeys = ['u7', 'u10', 'u13', 'u15'];
        $coachKeys = ['head', 'technical', 'goalkeeper', 'fitness'];
        $types = ['technical', 'tactical', 'match_play', 'conditioning'];
        $sessions = [];
        $baseDate = Carbon::parse('2026-05-01');

        for ($i = 0; $i < 12; $i++) {
            $programKey = $programKeys[$i % count($programKeys)];
            $coachKey = $coachKeys[$i % count($coachKeys)];
            $date = $baseDate->copy()->addDays($i * 3)->format('Y-m-d');

            $sessions[] = TrainingSession::query()->updateOrCreate(
                [
                    'season_id' => $season->id,
                    'program_id' => $programs[$programKey]->id,
                    'title' => strtoupper($programKey).' Session '.($i + 1),
                    'date' => $date,
                ],
                [
                    'coach_id' => $coaches[$coachKey]->id,
                    'session_type' => $types[$i % count($types)],
                    'start_time' => sprintf('%02d:00:00', 8 + ($i % 6)),
                    'end_time' => sprintf('%02d:30:00', 9 + ($i % 6)),
                    'location' => 'Pitch '.chr(65 + ($i % 3)).', Powerblink Academy',
                    'notes' => 'Showcase training session for Powerblink FC.',
                ]
            );
        }

        return $sessions;
    }

    /**
     * @param  list<TrainingSession>  $sessions
     * @param  array<string, Player>  $players
     */
    private function seedExpandedAttendance(array $sessions, array $players): void
    {
        $statusCycle = ['present', 'present', 'absent', 'late', 'present'];
        $playerList = array_values($players);
        $statusIndex = 0;

        foreach ($sessions as $sessionIndex => $session) {
            foreach ($playerList as $playerIndex => $player) {
                $status = $statusCycle[($statusIndex + $sessionIndex + $playerIndex) % count($statusCycle)];

                SessionAttendance::query()->updateOrCreate(
                    [
                        'training_session_id' => $session->id,
                        'player_id' => $player->id,
                    ],
                    [
                        'status' => $status,
                        'remarks' => $status === 'absent' ? 'Reported unavailable' : ($status === 'late' ? 'Arrived 10 minutes late' : null),
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, Player>  $players
     */
    private function seedExpandedPerformanceReports(Season $season, array $players, Coach $coach): void
    {
        $metrics = [
            ['passing' => 82, 'dribbling' => 78, 'speed' => 80, 'fitness' => 85, 'discipline' => 88, 'teamwork' => 90],
            ['passing' => 75, 'dribbling' => 80, 'speed' => 77, 'fitness' => 82, 'discipline' => 85, 'teamwork' => 86],
            ['passing' => 88, 'dribbling' => 84, 'speed' => 83, 'fitness' => 87, 'discipline' => 90, 'teamwork' => 92],
            ['passing' => 70, 'dribbling' => 72, 'speed' => 74, 'fitness' => 78, 'discipline' => 80, 'teamwork' => 82],
        ];

        foreach (array_values($players) as $index => $player) {
            $m = $metrics[$index % count($metrics)];
            $overall = round(array_sum($m) / count($m), 1);

            PerformanceReport::query()->updateOrCreate(
                [
                    'season_id' => $season->id,
                    'player_id' => $player->id,
                    'coach_id' => $coach->id,
                    'reported_at' => Carbon::parse('2026-06-01')->addDays($index),
                ],
                [
                    ...$m,
                    'overall_score' => $overall,
                    'comments' => 'Showcase performance report — steady progress across technical and tactical blocks.',
                ]
            );
        }
    }

    /**
     * @param  array<string, Player>  $players
     * @return list<Tournament>
     */
    private function seedTournaments(Season $season, array $players): array
    {
        $definitions = [
            [
                'title' => 'Powerblink Independence Day Tournament',
                'category' => 'U13-U15',
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-03',
                'status' => 'upcoming',
                'image' => 'home-powerblink-fc-054.jpg',
            ],
            [
                'title' => 'Lekki Youth Cup 2026',
                'category' => 'U10-U13',
                'start_date' => '2026-08-15',
                'end_date' => '2026-08-17',
                'status' => 'upcoming',
                'image' => 'home-powerblink-fc-047.jpg',
            ],
            [
                'title' => 'Spring Academy Showcase 2026',
                'category' => 'All Ages',
                'start_date' => '2026-03-20',
                'end_date' => '2026-03-22',
                'status' => 'completed',
                'image' => 'home-powerblink-fc-048.jpg',
            ],
        ];

        $tournaments = [];
        $playerList = array_values($players);

        foreach ($definitions as $definition) {
            $tournament = Tournament::query()->updateOrCreate(
                [
                    'season_id' => $season->id,
                    'title' => $definition['title'],
                ],
                [
                    'category' => $definition['category'],
                    'start_date' => $definition['start_date'],
                    'end_date' => $definition['end_date'],
                    'location' => 'Powerblink Academy Grounds, Ibeju Lekki',
                    'description' => 'Regional youth showcase featuring Powerblink FC squads and invited clubs.',
                    'status' => $definition['status'],
                    'max_teams' => 12,
                    'featured_image_media_id' => $this->mediaIdForPath($this->image($definition['image'])),
                ]
            );

            foreach (array_slice($playerList, 0, min(8, count($playerList))) as $player) {
                TournamentSquad::query()->updateOrCreate(
                    [
                        'tournament_id' => $tournament->id,
                        'player_id' => $player->id,
                    ],
                    ['position' => $player->primary_position]
                );
            }

            $tournaments[] = $tournament;
        }

        return $tournaments;
    }

    private function seedExpandedAnnouncements(Season $season, User $adminUser): void
    {
        $announcements = [
            ['title' => '2026 Season Registration Now Open', 'body' => 'Applications are open for U7 through U15 programs.', 'audience' => 'parents', 'published_at' => '2026-01-05 08:00:00'],
            ['title' => 'Independence Day Tournament Squad Selection', 'body' => 'Coaches will announce preliminary tournament squads after the June assessment window.', 'audience' => 'all', 'published_at' => '2026-06-10 09:00:00'],
            ['title' => 'Parent-Coach Meeting — July Schedule', 'body' => 'Monthly parent-coach meetings resume every first Saturday at 10:00 AM.', 'audience' => 'parents', 'published_at' => '2026-06-12 09:00:00'],
            ['title' => 'U15 Elite Trial Day', 'body' => 'Selected U15 players invited for advanced trial sessions on Pitch B.', 'audience' => 'players', 'published_at' => '2026-06-14 11:00:00'],
            ['title' => 'Coach Development Workshop', 'body' => 'All coaching staff attend safeguarding and curriculum refresh workshop.', 'audience' => 'coaches', 'published_at' => '2026-06-16 08:30:00'],
            ['title' => 'Payment Reminder — July Installments', 'body' => 'Families on installment plans should complete July dues by the 5th.', 'audience' => 'parents', 'published_at' => '2026-06-18 07:00:00'],
        ];

        foreach ($announcements as $announcement) {
            Announcement::query()->updateOrCreate(
                ['season_id' => $season->id, 'title' => $announcement['title']],
                [
                    'body' => $announcement['body'],
                    'audience' => $announcement['audience'],
                    'channel' => 'in_app',
                    'published_at' => Carbon::parse($announcement['published_at']),
                    'created_by' => $adminUser->id,
                ]
            );
        }
    }

    /**
     * @param  array<string, Player>  $players
     */
    private function seedExpandedAcademyPayments(array $players, Season $season): void
    {
        $references = [];
        $playerList = array_values($players);

        foreach ($playerList as $index => $player) {
            $references[] = [
                'reference' => sprintf('PB-PAY-MONTHLY-%03d', $index + 1),
                'player' => $player,
                'status' => $index % 4 === 0 ? 'pending' : 'success',
                'amount' => 4500000 + ($index * 250000),
                'paid_at' => $index % 4 === 0 ? null : Carbon::parse('2026-06-01')->addDays($index % 20),
            ];
        }

        foreach ($references as $definition) {
            AcademyPayment::query()->updateOrCreate(
                ['reference' => $definition['reference']],
                [
                    'player_id' => $definition['player']->id,
                    'season_id' => $season->id,
                    'type' => 'monthly_fee',
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
    private function seedExpandedInstallmentPlans(array $registrations, array $players): void
    {
        $installments = [
            ['registration_id' => $registrations['awaiting_payment']->id, 'player_id' => null, 'amount' => 1333333, 'due_date' => '2026-07-01', 'status' => 'pending'],
            ['registration_id' => $registrations['awaiting_payment']->id, 'player_id' => null, 'amount' => 1333333, 'due_date' => '2026-08-01', 'status' => 'pending'],
            ['registration_id' => $registrations['awaiting_payment']->id, 'player_id' => null, 'amount' => 1333334, 'due_date' => '2026-09-01', 'status' => 'pending'],
            ['registration_id' => $registrations['activated']->id, 'player_id' => $players['demo']->id, 'amount' => 3500000, 'due_date' => '2026-01-13', 'status' => 'paid'],
            ['registration_id' => $registrations['activated']->id, 'player_id' => $players['demo']->id, 'amount' => 6500000, 'due_date' => '2026-06-01', 'status' => 'paid'],
            ['registration_id' => $registrations['awaiting_payment']->id, 'player_id' => null, 'amount' => 1333333, 'due_date' => '2026-06-01', 'status' => 'overdue'],
        ];

        foreach ($installments as $installment) {
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

    /**
     * @param  array<string, Player>  $players
     * @param  array<string, Registration>  $registrations
     */
    private function seedPlayerDocuments(array $players, array $registrations, User $adminUser): void
    {
        $docTypes = ['birth_certificate', 'medical_form', 'passport_photo'];
        $mediaFiles = [
            'registration-powerblink-fc-078.jpg',
            'registration-powerblink-fc-079.jpg',
            'registrations-powerblink-fc-080.jpg',
        ];

        $targets = [
            ['player' => $players['demo'], 'registration' => $registrations['activated']],
            ['player' => $players['squad'], 'registration' => null],
            ['player' => $players['squad2'], 'registration' => null],
        ];

        foreach ($targets as $targetIndex => $target) {
            foreach ($docTypes as $typeIndex => $type) {
                $mediaId = $this->mediaIdForPath($this->image($mediaFiles[($targetIndex + $typeIndex) % count($mediaFiles)]));

                PlayerDocument::query()->updateOrCreate(
                    [
                        'player_id' => $target['player']->id,
                        'document_type' => $type,
                    ],
                    [
                        'registration_id' => $target['registration']?->id,
                        'media_id' => $mediaId,
                        'status' => $typeIndex === 0 ? 'verified' : 'pending',
                        'verified_by' => $typeIndex === 0 ? $adminUser->id : null,
                        'verified_at' => $typeIndex === 0 ? Carbon::parse('2026-01-14 10:00:00') : null,
                    ]
                );
            }
        }
    }

    private function seedDemoNotifications(User $adminUser, User $coachUser, User $parentUser, User $playerUser): void
    {
        $definitions = [
            [$adminUser, 'New registration submitted', 'A parent submitted a new player registration for review.', '/admin/registrations'],
            [$adminUser, 'Payment received', 'Registration fee payment confirmed for PB-REG-2026-001.', '/admin/payments'],
            [$coachUser, 'Session roster updated', 'Attendance has been updated for your upcoming U13 session.', '/admin/attendance'],
            [$coachUser, 'Performance reports due', 'Submit June performance reports for your squad.', '/admin/performance-reports'],
            [$parentUser, 'Registration approved', 'Your registration has been approved. Complete payment to activate.', '/register/pay'],
            [$parentUser, 'Installment reminder', 'Your July installment is due on the 1st.', '/portal'],
            [$playerUser, 'Training session tomorrow', 'U13 technical session scheduled for 4:00 PM on Pitch A.', '/portal'],
            [$playerUser, 'Performance report published', 'Your June performance report is now available.', '/portal'],
            [$adminUser, 'Tournament squad finalized', 'Independence Day tournament squads have been published.', '/admin/tournaments'],
            [$parentUser, 'Academy announcement', 'Parent-coach meeting scheduled for the first Saturday of July.', '/portal'],
        ];

        foreach ($definitions as [$user, $title, $body, $url]) {
            $user->notify(new AcademyAlert($title, $body, $url));
        }
    }

    private function seedExpandedGallery(): void
    {
        $items = [
            ['title' => 'Golden Hour Training', 'category' => 'training', 'image' => 'home-powerblink-fc-044.jpg', 'sort_order' => 1],
            ['title' => 'U7 Grassroots Joy', 'category' => 'programs', 'image' => 'home-powerblink-fc-046.jpg', 'sort_order' => 2],
            ['title' => 'Tactical Briefing', 'category' => 'coaching', 'image' => 'home-powerblink-fc-045.jpg', 'sort_order' => 3],
            ['title' => 'Tournament Action', 'category' => 'tournaments', 'image' => 'home-powerblink-fc-054.jpg', 'sort_order' => 4],
            ['title' => 'Academy Facilities', 'category' => 'facilities', 'image' => 'about-us-powerblink-fc-001.jpg', 'sort_order' => 5],
            ['title' => 'Match Day Energy', 'category' => 'tournaments', 'image' => 'home-powerblink-fc-047.jpg', 'sort_order' => 6],
            ['title' => 'Goalkeeper Training', 'category' => 'training', 'image' => 'home-powerblink-fc-048.jpg', 'sort_order' => 7],
            ['title' => 'Team Celebration', 'category' => 'programs', 'image' => 'home-powerblink-fc-049.jpg', 'sort_order' => 8],
        ];

        foreach ($items as $item) {
            $mediaId = $this->mediaIdForPath($this->image($item['image']));
            if ($mediaId === null) {
                continue;
            }

            GalleryItem::query()->updateOrCreate(
                ['media_id' => $mediaId, 'title' => $item['title']],
                [
                    'category' => $item['category'],
                    'sort_order' => $item['sort_order'],
                    'is_published' => true,
                ]
            );
        }
    }

    private function seedFaqPageSections(): void
    {
        $sections = [
            ['section_key' => 'kicker', 'content_type' => 'text', 'content' => 'Need Help?'],
            ['section_key' => 'heading', 'content_type' => 'text', 'content' => 'HELP CENTER'],
            ['section_key' => 'intro', 'content_type' => 'textarea', 'content' => 'Common questions about registration, training, and academy policies at Powerblink FC.'],
            ['section_key' => 'hero_image', 'content_type' => 'image', 'content' => 'asset/images/powerblink/home-powerblink-fc-044.jpg'],
            ['section_key' => 'cat_1_title', 'content_type' => 'text', 'content' => 'Registration'],
            ['section_key' => 'cat_1_icon', 'content_type' => 'text', 'content' => 'how_to_reg'],
            ['section_key' => 'cat_1_faqs', 'content_type' => 'json', 'content' => json_encode([
                ['q' => 'When can I pay the registration fee?', 'a' => 'Payment is only available after your application is approved. You will receive an email with a secure payment link.'],
                ['q' => 'How long does review take?', 'a' => 'Our team typically reviews applications within a few business days.'],
            ])],
            ['section_key' => 'cat_2_title', 'content_type' => 'text', 'content' => 'Programs'],
            ['section_key' => 'cat_2_icon', 'content_type' => 'text', 'content' => 'sports_soccer'],
            ['section_key' => 'cat_2_faqs', 'content_type' => 'json', 'content' => json_encode([
                ['q' => 'What age groups do you serve?', 'a' => 'We offer pathways from U7 through U15.'],
                ['q' => 'How often do teams train?', 'a' => 'Frequency depends on the program — typically 2–4 sessions per week.'],
            ])],
            ['section_key' => 'cat_3_title', 'content_type' => 'text', 'content' => 'Training'],
            ['section_key' => 'cat_3_icon', 'content_type' => 'text', 'content' => 'calendar_month'],
            ['section_key' => 'cat_3_faqs', 'content_type' => 'json', 'content' => json_encode([
                ['q' => 'What should my child bring to training?', 'a' => 'Boots, shin guards, training kit, and a water bottle.'],
            ])],
            ['section_key' => 'cat_4_title', 'content_type' => 'text', 'content' => 'Medical'],
            ['section_key' => 'cat_4_icon', 'content_type' => 'text', 'content' => 'health_and_safety'],
            ['section_key' => 'cat_4_faqs', 'content_type' => 'json', 'content' => json_encode([
                ['q' => 'What medical information is required?', 'a' => 'Please disclose allergies, relevant medical history, and fitness clearance during registration.'],
            ])],
            ['section_key' => 'cta_title', 'content_type' => 'text', 'content' => 'STILL HAVE QUESTIONS?'],
            ['section_key' => 'cta_body', 'content_type' => 'textarea', 'content' => 'Contact our academy office Monday through Saturday for registration and program support.'],
            ['section_key' => 'cta_image', 'content_type' => 'image', 'content' => 'asset/images/powerblink/contact-us-powerblink-fc-034.jpg'],
        ];

        foreach ($sections as $section) {
            PageSection::query()->updateOrCreate(
                ['page' => 'faq', 'section_key' => $section['section_key']],
                [
                    'content_type' => $section['content_type'],
                    'content' => $section['content'],
                ]
            );
        }
    }

    private function seedSiteTrafficEventsWithAcademyUrl(): void
    {
        SiteTrafficEvent::query()->delete();

        $base = rtrim((string) config('powerblink.site_url', 'https://powerblinkfc.com'), '/');

        $paths = [
            ['path' => '/', 'route_name' => 'home', 'views' => 42],
            ['path' => '/programs', 'route_name' => 'programs', 'views' => 28],
            ['path' => '/about', 'route_name' => 'about', 'views' => 19],
            ['path' => '/register', 'route_name' => 'registration.wizard', 'views' => 35],
            ['path' => '/gallery', 'route_name' => 'gallery', 'views' => 15],
            ['path' => '/contact', 'route_name' => 'contact', 'views' => 12],
            ['path' => '/tournaments', 'route_name' => 'tournaments', 'views' => 11],
        ];

        $sessionBase = 'demo-session-';
        $day = now()->subDays(30);

        foreach ($paths as $pathDef) {
            for ($i = 0; $i < $pathDef['views']; $i++) {
                SiteTrafficEvent::query()->create([
                    'path' => $pathDef['path'],
                    'route_name' => $pathDef['route_name'],
                    'url' => $base.$pathDef['path'],
                    'method' => 'GET',
                    'referrer_host' => $i % 3 === 0 ? 'google.com' : ($i % 3 === 1 ? 'instagram.com' : null),
                    'session_id' => $sessionBase.($i % 12),
                    'viewed_at' => $day->copy()->addDays($i % 28)->addHours($i % 8),
                ]);
            }
        }
    }
}
