<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTournamentController extends Controller
{
    public function index(): View
    {
        return view('admin.tournaments.index', [
            'title' => __('Tournaments'),
            'tournaments' => Tournament::query()->with('season')->orderByDesc('start_date')->paginate(20),
        ]);
    }

    public function show(Tournament $tournament): View
    {
        $tournament->load(['season', 'featuredImage', 'squads.player']);

        return view('admin.tournaments.show', [
            'title' => $tournament->title,
            'tournament' => $tournament,
        ]);
    }

    public function create(): View
    {
        return view('admin.tournaments.create', [
            'title' => __('Add tournament'),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:64'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:32'],
        ]);

        $tournament = Tournament::query()->create($data);

        return redirect()->route('admin.tournaments.show', $tournament)->with('status', __('Tournament created.'));
    }

    public function edit(Tournament $tournament): View
    {
        return view('admin.tournaments.edit', [
            'title' => __('Edit tournament'),
            'tournament' => $tournament,
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function update(Request $request, Tournament $tournament): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:64'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:32'],
        ]);

        $tournament->update($data);

        return redirect()->route('admin.tournaments.show', $tournament)->with('status', __('Tournament updated.'));
    }

    public function destroy(Tournament $tournament): RedirectResponse
    {
        $tournament->delete();

        return redirect()->route('admin.tournaments.index')->with('status', __('Tournament removed.'));
    }
}
