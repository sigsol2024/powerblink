<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.training-sessions.show', $session)"
    :back-label="__('Session details')"
    :subtitle="__('Edit session')"
  />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.training-sessions.update', $session) }}" class="pb-admin-form">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">{{ __('Season') }}</label>
            <select name="season_id"  required>
              @foreach ($seasons as $season)<option value="{{ $season->id }}" @selected($session->season_id == $season->id)>{{ $season->name }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">{{ __('Program') }}</label>
            <select name="program_id"  required>
              @foreach ($programs as $program)<option value="{{ $program->id }}" @selected($session->program_id == $program->id)>{{ $program->name }}</option>@endforeach
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ __('Coach') }}</label>
          <select name="coach_id" >
            <option value="">{{ __('Unassigned') }}</option>
            @foreach ($coaches as $coach)<option value="{{ $coach->id }}" @selected($session->coach_id == $coach->id)>{{ $coach->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title" value="{{ old('title', $session->title) }}"  required></div>
        <div><label class="block text-sm font-medium">{{ __('Date') }}</label><input type="date" name="date" value="{{ old('date', $session->date?->format('Y-m-d')) }}"  required></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Start') }}</label><input type="time" name="start_time" value="{{ old('start_time', $session->start_time) }}" ></div>
          <div><label class="block text-sm font-medium">{{ __('End') }}</label><input type="time" name="end_time" value="{{ old('end_time', $session->end_time) }}" ></div>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Location') }}</label><input type="text" name="location" value="{{ old('location', $session->location) }}" ></div>
        <x-admin.button type="submit">{{ __('Save') }}</x-admin.button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
