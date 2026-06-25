<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Guardian;
use App\Models\Player;
use App\Models\Registration;
use App\Models\SessionAttendance;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        if ($user->isParent()) {
            $guardian = Guardian::query()->where('user_id', $user->id)->first();
            $players = $guardian
                ? Player::query()->where('guardian_id', $guardian->id)->with('program')->get()
                : collect();
            $registrations = $guardian
                ? Registration::query()->where('guardian_id', $guardian->id)->with('program')->latest('submitted_at')->take(10)->get()
                : collect();

            return view('portal.parent.dashboard', [
                'title' => __('Parent Dashboard'),
                'guardian' => $guardian,
                'players' => $players,
                'registrations' => $registrations,
                'pendingRegistrations' => $registrations->whereIn('status', ['pending_review', 'awaiting_payment'])->count(),
                'announcements' => Announcement::query()->latest('published_at')->take(5)->get(),
            ]);
        }

        if ($user->isPlayer()) {
            $player = Player::query()->where('user_id', $user->id)->with(['program', 'season', 'photo'])->first();
            $attendance = $player
                ? SessionAttendance::query()
                    ->where('player_id', $player->id)
                    ->with('trainingSession')
                    ->latest()
                    ->take(10)
                    ->get()
                : collect();

            $attendanceTotal = $player
                ? SessionAttendance::query()->where('player_id', $player->id)->count()
                : 0;
            $presentCount = $player
                ? SessionAttendance::query()->where('player_id', $player->id)->where('status', 'present')->count()
                : 0;
            $attendanceRate = $attendanceTotal > 0 ? (int) round(($presentCount / $attendanceTotal) * 100) : null;

            $upcomingSessions = $player
                ? TrainingSession::query()
                    ->where('program_id', $player->program_id)
                    ->where('date', '>=', now()->toDateString())
                    ->orderBy('date')
                    ->take(5)
                    ->get()
                : collect();

            $monthSessions = $player
                ? TrainingSession::query()
                    ->where('program_id', $player->program_id)
                    ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                    ->count()
                : 0;

            return view('portal.player.dashboard', [
                'title' => __('Player Dashboard'),
                'player' => $player,
                'attendance' => $attendance,
                'attendanceRate' => $attendanceRate,
                'monthSessions' => $monthSessions,
                'nextSession' => $upcomingSessions->first(),
                'upcomingSessions' => $upcomingSessions,
                'announcements' => Announcement::query()->latest('published_at')->take(5)->get(),
            ]);
        }

        if ($user->isCoach()) {
            $coach = $user->coachProfile?->load('photo');
            $sessions = $coach
                ? TrainingSession::query()
                    ->where('coach_id', $coach->id)
                    ->where('date', '>=', now()->toDateString())
                    ->with('program')
                    ->orderBy('date')
                    ->take(10)
                    ->get()
                : collect();

            $weekSessions = $coach
                ? TrainingSession::query()
                    ->where('coach_id', $coach->id)
                    ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                    ->count()
                : 0;

            return view('portal.coach.dashboard', [
                'title' => __('Coach Dashboard'),
                'coach' => $coach,
                'sessions' => $sessions,
                'weekSessions' => $weekSessions,
                'playerCount' => $coach
                    ? Player::query()->whereIn('program_id', TrainingSession::query()->where('coach_id', $coach->id)->pluck('program_id'))->count()
                    : 0,
                'announcements' => Announcement::query()->latest('published_at')->take(5)->get(),
            ]);
        }

        abort(403);
    }
}
