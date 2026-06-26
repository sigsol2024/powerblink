<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.coaches.index')"
    :back-label="__('Coaching staff')"
    :subtitle="__('Add coach')"
  />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.coaches.store') }}" class="pb-admin-form">
        @csrf
        @foreach (['name' => __('Name'), 'title' => __('Title'), 'specialization' => __('Specialization'), 'email' => __('Email'), 'phone' => __('Phone')] as $field => $label)
          <div>
            <label class="block text-sm font-medium">{{ $label }}</label>
            <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" value="{{ old($field) }}"  @if($field==='name') required @endif>
          </div>
        @endforeach
        <div>
          <label class="block text-sm font-medium">{{ __('Bio') }}</label>
          <textarea name="bio" rows="4" >{{ old('bio') }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> {{ __('Active') }}</label>
        <x-admin.button type="submit">{{ __('Create') }}</x-admin.button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
