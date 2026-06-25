<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Program;
use App\Models\Season;
use App\Models\TrainingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTrainingSessionController extends Controller
{
    public function index(Request $request): View
    {
        $query = TrainingSession::query()->with(['program', 'coach', 'season'])->orderByDesc('date');

        if ($programId = (int) $request->query('program_id')) {
            $query->where('program_id', $programId);
        }

        return view('admin.training-sessions.index', [
            'title' => __('Training sessions'),
            'sessions' => $query->paginate(20)->withQueryString(),
            'programs' => Program::query()->orderBy('name')->get(),
            'programId' => $programId,
        ]);
    }

    public function show(TrainingSession $trainingSession): View
    {
        $trainingSession->load(['program', 'coach', 'season', 'attendance.player']);

        return view('admin.training-sessions.show', [
            'title' => $trainingSession->title,
            'session' => $trainingSession,
        ]);
    }

    public function create(): View
    {
        return view('admin.training-sessions.create', [
            'title' => __('Schedule session'),
            'programs' => Program::query()->orderBy('name')->get(),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
            'coaches' => Coach::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'title' => ['required', 'string', 'max:255'],
            'session_type' => ['nullable', 'string', 'max:64'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'string', 'max:8'],
            'end_time' => ['nullable', 'string', 'max:8'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $session = TrainingSession::query()->create($data);

        return redirect()->route('admin.training-sessions.show', $session)->with('status', __('Session created.'));
    }

    public function edit(TrainingSession $trainingSession): View
    {
        return view('admin.training-sessions.edit', [
            'title' => __('Edit session'),
            'session' => $trainingSession,
            'programs' => Program::query()->orderBy('name')->get(),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
            'coaches' => Coach::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, TrainingSession $trainingSession): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'title' => ['required', 'string', 'max:255'],
            'session_type' => ['nullable', 'string', 'max:64'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'string', 'max:8'],
            'end_time' => ['nullable', 'string', 'max:8'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $trainingSession->update($data);

        return redirect()->route('admin.training-sessions.show', $trainingSession)->with('status', __('Session updated.'));
    }

    public function destroy(TrainingSession $trainingSession): RedirectResponse
    {
        $trainingSession->delete();

        return redirect()->route('admin.training-sessions.index')->with('status', __('Session removed.'));
    }
}
