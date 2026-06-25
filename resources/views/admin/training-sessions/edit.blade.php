<x-app-layout>
  <x-admin.page-header :title="__('Edit session')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.training-sessions.update', $session) }}" class="space-y-4 max-w-xl">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">{{ __('Season') }}</label>
            <select name="season_id" class="mt-1 w-full rounded-lg border-pb-border" required>
              @foreach ($seasons as $season)<option value="{{ $season->id }}" @selected($session->season_id == $season->id)>{{ $season->name }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">{{ __('Program') }}</label>
            <select name="program_id" class="mt-1 w-full rounded-lg border-pb-border" required>
              @foreach ($programs as $program)<option value="{{ $program->id }}" @selected($session->program_id == $program->id)>{{ $program->name }}</option>@endforeach
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ __('Coach') }}</label>
          <select name="coach_id" class="mt-1 w-full rounded-lg border-pb-border">
            <option value="">{{ __('Unassigned') }}</option>
            @foreach ($coaches as $coach)<option value="{{ $coach->id }}" @selected($session->coach_id == $coach->id)>{{ $coach->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title" value="{{ old('title', $session->title) }}" class="mt-1 w-full rounded-lg border-pb-border" required></div>
        <div><label class="block text-sm font-medium">{{ __('Date') }}</label><input type="date" name="date" value="{{ old('date', $session->date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-pb-border" required></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Start') }}</label><input type="time" name="start_time" value="{{ old('start_time', $session->start_time) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
          <div><label class="block text-sm font-medium">{{ __('End') }}</label><input type="time" name="end_time" value="{{ old('end_time', $session->end_time) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Location') }}</label><input type="text" name="location" value="{{ old('location', $session->location) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-pb-navy text-white font-semibold text-sm">{{ __('Save') }}</button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
