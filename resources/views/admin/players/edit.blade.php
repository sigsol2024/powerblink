<x-app-layout>
  <x-admin.page-header :title="__('Edit player')" />
  <x-admin.page-content>
    <form method="POST" action="{{ route('admin.players.update', $player) }}" class="max-w-xl space-y-4">
      @csrf
      @method('PUT')
      @include('admin.players._form')
      <button type="submit" class="bg-secondary text-on-secondary px-4 py-2 rounded-lg font-semibold text-sm">{{ __('Update') }}</button>
    </form>
  </x-admin.page-content>
</x-app-layout>
