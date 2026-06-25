<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Player;
use App\Models\Program;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlayerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Player::query()->with(['program', 'season', 'guardian'])->latest();

        if ($status = trim((string) $request->query('status', ''))) {
            $query->where('status', $status);
        }

        return view('admin.players.index', [
            'title' => __('Players'),
            'players' => $query->paginate(20)->withQueryString(),
            'status' => $status,
        ]);
    }

    public function show(Player $player): View
    {
        $player->load(['program', 'season', 'guardian', 'photo']);

        return view('admin.players.show', [
            'title' => $player->name,
            'player' => $player,
        ]);
    }

    public function create(): View
    {
        return view('admin.players.create', [
            'title' => __('Add player'),
            'programs' => Program::query()->where('is_active', true)->orderBy('name')->get(),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
            'guardians' => Guardian::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'guardian_id' => ['required', 'exists:guardians,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'season_id' => ['required', 'exists:seasons,id'],
            'date_of_birth' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $season = Season::query()->findOrFail($data['season_id']);
        $data['player_code'] = $this->generatePlayerCode((int) $season->id, $season->start_date?->year);

        $player = Player::query()->create($data);

        return redirect()->route('admin.players.show', $player)->with('status', __('Player created.'));
    }

    public function edit(Player $player): View
    {
        return view('admin.players.edit', [
            'title' => __('Edit player'),
            'player' => $player,
            'programs' => Program::query()->orderBy('name')->get(),
            'seasons' => Season::query()->orderByDesc('start_date')->get(),
            'guardians' => Guardian::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Player $player): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'guardian_id' => ['required', 'exists:guardians,id'],
            'program_id' => ['required', 'exists:programs,id'],
            'season_id' => ['required', 'exists:seasons,id'],
            'date_of_birth' => ['nullable', 'date'],
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $player->update($data);

        return redirect()->route('admin.players.show', $player)->with('status', __('Player updated.'));
    }

    public function destroy(Player $player): RedirectResponse
    {
        $player->delete();

        return redirect()->route('admin.players.index')->with('status', __('Player removed.'));
    }

    private function generatePlayerCode(int $seasonId, ?int $year = null): string
    {
        $year = $year ?? now()->year;

        do {
            $sequence = Player::query()->where('season_id', $seasonId)->count() + 1;
            $code = sprintf('PB-%d-%04d', $year, $sequence);
        } while (Player::query()->where('player_code', $code)->exists());

        return $code;
    }
}
