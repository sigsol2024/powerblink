<x-app-layout>
  <x-admin.page-header :title="__('Edit program')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="space-y-4 max-w-xl">
        @csrf @method('PUT')
        <div>
          <label class="block text-sm font-medium">{{ __('Season') }}</label>
          <select name="season_id" class="mt-1 w-full rounded-lg border-pb-border" required>
            @foreach ($seasons as $season)
              <option value="{{ $season->id }}" @selected($program->season_id == $season->id)>{{ $season->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ __('Name') }}</label>
          <input type="text" name="name" value="{{ old('name', $program->name) }}" class="mt-1 w-full rounded-lg border-pb-border" required>
        </div>
        <div>
          <label class="block text-sm font-medium">{{ __('Age group') }}</label>
          <input type="text" name="age_group" value="{{ old('age_group', $program->age_group) }}" class="mt-1 w-full rounded-lg border-pb-border">
        </div>
        <div>
          <label class="block text-sm font-medium">{{ __('Description') }}</label>
          <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-pb-border">{{ old('description', $program->description) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium">{{ __('Registration fee') }}</label>
            <input type="number" name="registration_fee" value="{{ old('registration_fee', $program->registration_fee) }}" class="mt-1 w-full rounded-lg border-pb-border" min="0">
          </div>
          <div>
            <label class="block text-sm font-medium">{{ __('Monthly fee') }}</label>
            <input type="number" name="monthly_fee" value="{{ old('monthly_fee', $program->monthly_fee) }}" class="mt-1 w-full rounded-lg border-pb-border" min="0">
          </div>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $program->is_active))> {{ __('Active') }}</label>
        <button type="submit" class="px-4 py-2 rounded-lg bg-pb-navy text-white font-semibold text-sm">{{ __('Save') }}</button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
