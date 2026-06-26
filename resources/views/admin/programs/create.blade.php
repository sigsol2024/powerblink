<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.programs.index')"
    :back-label="__('All programs')"
    :subtitle="__('Add program')"
  />

  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.programs.store') }}" class="pb-admin-form">
        @csrf
        <div class="pb-field">
          <label for="season_id">{{ __('Season') }}</label>
          <select id="season_id" name="season_id" required>
            @foreach ($seasons as $season)
              <option value="{{ $season->id }}">{{ $season->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="pb-field">
          <label for="name">{{ __('Name') }}</label>
          <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="pb-field">
          <label for="age_group">{{ __('Age group') }}</label>
          <input type="text" id="age_group" name="age_group" value="{{ old('age_group') }}">
        </div>
        <div class="pb-field">
          <label for="description">{{ __('Description') }}</label>
          <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="pb-field">
            <label for="registration_fee">{{ __('Registration fee') }}</label>
            <input type="number" id="registration_fee" name="registration_fee" value="{{ old('registration_fee', 0) }}" min="0">
          </div>
          <div class="pb-field">
            <label for="monthly_fee">{{ __('Monthly fee') }}</label>
            <input type="number" id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', 0) }}" min="0">
          </div>
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-on-surface">
          <input type="checkbox" name="is_active" value="1" checked>
          {{ __('Active') }}
        </label>
        <div class="flex flex-wrap gap-3 pt-2">
          <x-admin.button type="submit">{{ __('Create program') }}</x-admin.button>
          <x-admin.button variant="secondary" :href="route('admin.programs.index')">{{ __('Cancel') }}</x-admin.button>
        </div>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
