<x-app-layout>
  <x-admin.page-header :title="__('Schedule session')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.training-sessions.store') }}" class="pb-admin-form">
        @csrf
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">{{ __('Season') }}</label>
            <select name="season_id"  required>
              @foreach ($seasons as $season)<option value="{{ $season->id }}">{{ $season->name }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium">{{ __('Program') }}</label>
            <select name="program_id"  required>
              @foreach ($programs as $program)<option value="{{ $program->id }}">{{ $program->name }}</option>@endforeach
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ __('Coach') }}</label>
          <select name="coach_id" >
            <option value="">{{ __('Unassigned') }}</option>
            @foreach ($coaches as $coach)<option value="{{ $coach->id }}">{{ $coach->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title"  required></div>
        <div><label class="block text-sm font-medium">{{ __('Date') }}</label><input type="date" name="date"  required></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Start') }}</label><input type="time" name="start_time" ></div>
          <div><label class="block text-sm font-medium">{{ __('End') }}</label><input type="time" name="end_time" ></div>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Location') }}</label><input type="text" name="location" ></div>
        <x-admin.button type="submit">{{ __('Create') }}</x-admin.button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
