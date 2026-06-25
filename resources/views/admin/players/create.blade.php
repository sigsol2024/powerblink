<x-app-layout>
  <x-admin.page-header :title="__('Add player')" />
  <x-admin.page-content>
    <form method="POST" action="{{ route('admin.players.store') }}" class="max-w-xl space-y-4">
      @csrf
      @include('admin.players._form')
      <button type="submit" class="bg-secondary text-on-secondary px-4 py-2 rounded-lg font-semibold text-sm">{{ __('Save') }}</button>
    </form>
  </x-admin.page-content>
</x-app-layout>
