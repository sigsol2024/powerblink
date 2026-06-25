<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\PerformanceReport;
use App\Models\Player;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPerformanceReportController extends Controller
{
    public function index(): View
    {
        return view('admin.performance.index', [
            'title' => __('Performance'),
            'reports' => PerformanceReport::query()->with(['player', 'coach', 'season'])->latest('reported_at')->paginate(20),
        ]);
    }

    public function show(PerformanceReport $performanceReport): View
    {
        $performanceReport->load(['player', 'coach', 'season']);

        return view('admin.performance.show', [
            'title' => __('Performance report'),
            'report' => $performanceReport,
        ]);
    }

    public function create(): View
    {
        return view('admin.performance.create', [
            'title' => __('New performance report'),
            'players' => Player::query()->where('status', 'active')->orderBy('name')->get(),
            'coaches' => Coach::query()->where('is_active', true)->orderBy('name')->get(),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'player_id' => ['required', 'exists:players,id'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'passing' => ['nullable', 'integer', 'min:0', 'max:100'],
            'dribbling' => ['nullable', 'integer', 'min:0', 'max:100'],
            'speed' => ['nullable', 'integer', 'min:0', 'max:100'],
            'fitness' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discipline' => ['nullable', 'integer', 'min:0', 'max:100'],
            'teamwork' => ['nullable', 'integer', 'min:0', 'max:100'],
            'comments' => ['nullable', 'string'],
            'reported_at' => ['nullable', 'date'],
        ]);
        $data['reported_at'] = $data['reported_at'] ?? now();

        $report = PerformanceReport::query()->create($data);

        return redirect()->route('admin.performance.show', $report)->with('status', __('Report created.'));
    }

    public function edit(PerformanceReport $performanceReport): View
    {
        return view('admin.performance.edit', [
            'title' => __('Edit performance report'),
            'report' => $performanceReport,
            'players' => Player::query()->orderBy('name')->get(),
            'coaches' => Coach::query()->orderBy('name')->get(),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function update(Request $request, PerformanceReport $performanceReport): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'player_id' => ['required', 'exists:players,id'],
            'coach_id' => ['nullable', 'exists:coaches,id'],
            'passing' => ['nullable', 'integer', 'min:0', 'max:100'],
            'dribbling' => ['nullable', 'integer', 'min:0', 'max:100'],
            'speed' => ['nullable', 'integer', 'min:0', 'max:100'],
            'fitness' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discipline' => ['nullable', 'integer', 'min:0', 'max:100'],
            'teamwork' => ['nullable', 'integer', 'min:0', 'max:100'],
            'comments' => ['nullable', 'string'],
            'reported_at' => ['nullable', 'date'],
        ]);

        $performanceReport->update($data);

        return redirect()->route('admin.performance.show', $performanceReport)->with('status', __('Report updated.'));
    }
}
