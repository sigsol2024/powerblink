<x-app-layout>
  <x-admin.page-header :title="__('New performance report')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.performance.store') }}" class="pb-admin-form">
        @csrf
        <div>
          <label class="block text-sm font-medium">{{ __('Player') }}</label>
          <select name="player_id"  required>
            @foreach ($players as $player)<option value="{{ $player->id }}">{{ $player->name }}</option>@endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">{{ __('Season') }}</label>
            <select name="season_id"  required>
              @foreach ($seasons as $season)<option value="{{ $season->id }}">{{ $season->name }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">{{ __('Coach') }}</label>
            <select name="coach_id" >
              <option value="">{{ __('Unassigned') }}</option>
              @foreach ($coaches as $coach)<option value="{{ $coach->id }}">{{ $coach->name }}</option>@endforeach
            </select>
          </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
          @foreach (['passing','dribbling','speed','fitness','discipline','teamwork'] as $metric)
            <div>
              <label class="block text-xs text-on-surface-variant">{{ ucfirst($metric) }}</label>
              <input type="number" name="{{ $metric }}" min="0" max="100" >
            </div>
          @endforeach
        </div>
        <div><label class="block text-sm font-medium">{{ __('Comments') }}</label><textarea name="comments" rows="3" ></textarea></div>
        <x-admin.button type="submit">{{ __('Create') }}</x-admin.button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
