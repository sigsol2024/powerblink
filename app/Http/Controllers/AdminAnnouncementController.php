<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function index(): View
    {
        return view('admin.announcements.index', [
            'title' => __('Communications'),
            'announcements' => Announcement::query()->with('season')->latest('published_at')->paginate(20),
        ]);
    }

    public function show(Announcement $announcement): View
    {
        $announcement->load(['season', 'creator']);

        return view('admin.announcements.show', [
            'title' => $announcement->title,
            'announcement' => $announcement,
        ]);
    }

    public function create(): View
    {
        return view('admin.announcements.create', [
            'title' => __('New announcement'),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['nullable', 'exists:seasons,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['nullable', 'string', 'max:64'],
            'channel' => ['nullable', 'string', 'max:64'],
            'published_at' => ['nullable', 'date'],
        ]);
        $data['created_by'] = $request->user()->id;
        $data['published_at'] = $data['published_at'] ?? now();

        $announcement = Announcement::query()->create($data);

        return redirect()->route('admin.announcements.show', $announcement)->with('status', __('Announcement published.'));
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', [
            'title' => __('Edit announcement'),
            'announcement' => $announcement,
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validate([
            'season_id' => ['nullable', 'exists:seasons,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['nullable', 'string', 'max:64'],
            'channel' => ['nullable', 'string', 'max:64'],
            'published_at' => ['nullable', 'date'],
        ]);

        $announcement->update($data);

        return redirect()->route('admin.announcements.show', $announcement)->with('status', __('Announcement updated.'));
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', __('Announcement removed.'));
    }
}
