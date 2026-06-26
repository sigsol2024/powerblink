<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.coaches.show', $coach)"
    :back-label="__('Coach profile')"
    :subtitle="__('Edit coach')"
  />

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <form method="POST" action="{{ route('admin.coaches.update', $coach) }}" class="pb-admin-form">
        @csrf @method('PUT')
        @foreach (['name' => __('Name'), 'title' => __('Title'), 'specialization' => __('Specialization'), 'email' => __('Email'), 'phone' => __('Phone')] as $field => $label)
          <div class="pb-field">
            <label for="coach-{{ $field }}">{{ $label }}</label>
            <input type="{{ $field === 'email' ? 'email' : 'text' }}" id="coach-{{ $field }}" name="{{ $field }}" value="{{ old($field, $coach->$field) }}" @if($field==='name') required @endif>
          </div>
        @endforeach
        <div class="pb-field">
          <label for="coach-bio">{{ __('Bio') }}</label>
          <textarea id="coach-bio" name="bio" rows="4">{{ old('bio', $coach->bio) }}</textarea>
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-on-surface">
          <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coach->is_active))>
          {{ __('Active') }}
        </label>
        <div class="flex flex-wrap gap-3 pt-2">
          <x-admin.button type="submit">{{ __('Save changes') }}</x-admin.button>
          <x-admin.button variant="secondary" :href="route('admin.coaches.show', $coach)">{{ __('Cancel') }}</x-admin.button>
        </div>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
