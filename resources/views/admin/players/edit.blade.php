<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.players.show', $player)"
    :back-label="__('Player profile')"
    :subtitle="__('Edit player')"
  />

  <x-admin.page-content>
    @include('admin.partials.flash')

    <x-admin.card>
      <form method="POST" action="{{ route('admin.players.update', $player) }}" class="pb-admin-form">
        @csrf
        @method('PUT')
        @include('admin.players._form')
        <div class="flex flex-wrap gap-3 pt-2">
          <x-admin.button type="submit">{{ __('Save changes') }}</x-admin.button>
          <x-admin.button variant="secondary" :href="route('admin.players.show', $player)">{{ __('Cancel') }}</x-admin.button>
        </div>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
