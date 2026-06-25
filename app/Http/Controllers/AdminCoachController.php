<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCoachController extends Controller
{
    public function index(): View
    {
        return view('admin.coaches.index', [
            'title' => __('Coaches'),
            'coaches' => Coach::query()->with('photo')->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function show(Coach $coach): View
    {
        $coach->load(['photo', 'user', 'trainingSessions']);

        return view('admin.coaches.show', [
            'title' => $coach->name,
            'coach' => $coach,
        ]);
    }

    public function create(): View
    {
        return view('admin.coaches.create', [
            'title' => __('Add coach'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $coach = Coach::query()->create($data);

        return redirect()->route('admin.coaches.show', $coach)->with('status', __('Coach created.'));
    }

    public function edit(Coach $coach): View
    {
        return view('admin.coaches.edit', [
            'title' => __('Edit coach'),
            'coach' => $coach,
        ]);
    }

    public function update(Request $request, Coach $coach): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $coach->update($data);

        return redirect()->route('admin.coaches.show', $coach)->with('status', __('Coach updated.'));
    }

    public function destroy(Coach $coach): RedirectResponse
    {
        $coach->delete();

        return redirect()->route('admin.coaches.index')->with('status', __('Coach removed.'));
    }
}
