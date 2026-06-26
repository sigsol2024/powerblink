<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.programs.show', $program)"
    :back-label="__('Program details')"
    :subtitle="__('Edit program')"
  />

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="pb-admin-form">
        @csrf @method('PUT')
        <div class="pb-field">
          <label for="season_id">{{ __('Season') }}</label>
          <select id="season_id" name="season_id" required>
            @foreach ($seasons as $season)
              <option value="{{ $season->id }}" @selected($program->season_id == $season->id)>{{ $season->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="pb-field">
          <label for="name">{{ __('Name') }}</label>
          <input type="text" id="name" name="name" value="{{ old('name', $program->name) }}" required>
        </div>
        <div class="pb-field">
          <label for="age_group">{{ __('Age group') }}</label>
          <input type="text" id="age_group" name="age_group" value="{{ old('age_group', $program->age_group) }}">
        </div>
        <div class="pb-field">
          <label for="description">{{ __('Description') }}</label>
          <textarea id="description" name="description" rows="4">{{ old('description', $program->description) }}</textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="pb-field">
            <label for="registration_fee">{{ __('Registration fee') }}</label>
            <input type="number" id="registration_fee" name="registration_fee" value="{{ old('registration_fee', $program->registration_fee) }}" min="0">
          </div>
          <div class="pb-field">
            <label for="monthly_fee">{{ __('Monthly fee') }}</label>
            <input type="number" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', $program->monthly_fee) }}" min="0">
          </div>
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-on-surface">
          <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $program->is_active))>
          {{ __('Active') }}
        </label>
        <div class="flex flex-wrap gap-3 pt-2">
          <x-admin.button type="submit">{{ __('Save changes') }}</x-admin.button>
          <x-admin.button variant="secondary" :href="route('admin.programs.show', $program)">{{ __('Cancel') }}</x-admin.button>
        </div>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
