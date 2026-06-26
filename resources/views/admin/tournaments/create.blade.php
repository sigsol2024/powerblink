<x-app-layout>
  <x-admin.page-header :title="__('Add tournament')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.tournaments.store') }}" class="pb-admin-form">
        @csrf
        <div>
          <label class="block text-sm font-medium">{{ __('Season') }}</label>
          <select name="season_id"  required>
            @foreach ($seasons as $season)<option value="{{ $season->id }}">{{ $season->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title"  required></div>
        <div><label class="block text-sm font-medium">{{ __('Category') }}</label><input type="text" name="category" ></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Start date') }}</label><input type="date" name="start_date" ></div>
          <div><label class="block text-sm font-medium">{{ __('End date') }}</label><input type="date" name="end_date" ></div>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Location') }}</label><input type="text" name="location" ></div>
        <div><label class="block text-sm font-medium">{{ __('Description') }}</label><textarea name="description" rows="4" ></textarea></div>
        <x-admin.button type="submit">{{ __('Create') }}</x-admin.button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
