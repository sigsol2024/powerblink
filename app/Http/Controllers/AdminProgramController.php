<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProgramController extends Controller
{
    public function index(): View
    {
        return view('admin.programs.index', [
            'title' => __('Programs'),
            'programs' => Program::query()->with('season')->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function show(Program $program): View
    {
        $program->load(['season', 'heroImage', 'players']);

        return view('admin.programs.show', [
            'title' => $program->name,
            'program' => $program,
        ]);
    }

    public function create(): View
    {
        return view('admin.programs.create', [
            'title' => __('Add program'),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'name' => ['required', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'monthly_fee' => ['nullable', 'integer', 'min:0'],
            'registration_fee' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $program = Program::query()->create($data);

        return redirect()->route('admin.programs.show', $program)->with('status', __('Program created.'));
    }

    public function edit(Program $program): View
    {
        return view('admin.programs.edit', [
            'title' => __('Edit program'),
            'program' => $program,
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'name' => ['required', 'string', 'max:255'],
            'age_group' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'monthly_fee' => ['nullable', 'integer', 'min:0'],
            'registration_fee' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $program->update($data);

        return redirect()->route('admin.programs.show', $program)->with('status', __('Program updated.'));
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('admin.programs.index')->with('status', __('Program removed.'));
    }
}
