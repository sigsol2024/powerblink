<x-app-layout>
  <x-admin.page-header :title="__('Add coach')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.coaches.store') }}" class="space-y-4 max-w-xl">
        @csrf
        @foreach (['name' => __('Name'), 'title' => __('Title'), 'specialization' => __('Specialization'), 'email' => __('Email'), 'phone' => __('Phone')] as $field => $label)
          <div>
            <label class="block text-sm font-medium">{{ $label }}</label>
            <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" value="{{ old($field) }}" class="mt-1 w-full rounded-lg border-pb-border" @if($field==='name') required @endif>
          </div>
        @endforeach
        <div>
          <label class="block text-sm font-medium">{{ __('Bio') }}</label>
          <textarea name="bio" rows="4" class="mt-1 w-full rounded-lg border-pb-border">{{ old('bio') }}</textarea>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> {{ __('Active') }}</label>
        <button type="submit" class="px-4 py-2 rounded-lg bg-pb-navy text-white font-semibold text-sm">{{ __('Create') }}</button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
