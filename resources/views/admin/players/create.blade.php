<x-app-layout>
  <x-admin.page-header
    :back-href="route('admin.players.index')"
    :back-label="__('All players')"
    :subtitle="__('Add player')"
  />

  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.players.store') }}" class="pb-admin-form">
        @csrf
        @include('admin.players._form')
        <div class="flex flex-wrap gap-3 pt-2">
          <x-admin.button type="submit">{{ __('Create player') }}</x-admin.button>
          <x-admin.button variant="secondary" :href="route('admin.players.index')">{{ __('Cancel') }}</x-admin.button>
        </div>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
