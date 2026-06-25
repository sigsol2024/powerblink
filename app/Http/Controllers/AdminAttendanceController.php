<?php

namespace App\Http\Controllers;

use App\Models\SessionAttendance;
use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $sessionId = (int) $request->query('session_id');

        $sessions = TrainingSession::query()
            ->with('program')
            ->orderByDesc('date')
            ->take(30)
            ->get();

        $attendance = SessionAttendance::query()
            ->with(['player', 'trainingSession.program'])
            ->when($sessionId > 0, fn ($q) => $q->where('training_session_id', $sessionId))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.attendance.index', [
            'title' => __('Attendance'),
            'attendance' => $attendance,
            'sessions' => $sessions,
            'sessionId' => $sessionId,
        ]);
    }

    public function show(TrainingSession $trainingSession): View
    {
        $trainingSession->load(['program', 'coach', 'attendance.player']);

        return view('admin.attendance.show', [
            'title' => __('Session attendance'),
            'session' => $trainingSession,
        ]);
    }
}
