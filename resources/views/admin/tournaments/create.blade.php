<x-app-layout>
  <x-admin.page-header :title="__('Add tournament')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.tournaments.store') }}" class="space-y-4 max-w-xl">
        @csrf
        <div>
          <label class="block text-sm font-medium">{{ __('Season') }}</label>
          <select name="season_id" class="mt-1 w-full rounded-lg border-pb-border" required>
            @foreach ($seasons as $season)<option value="{{ $season->id }}">{{ $season->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title" class="mt-1 w-full rounded-lg border-pb-border" required></div>
        <div><label class="block text-sm font-medium">{{ __('Category') }}</label><input type="text" name="category" class="mt-1 w-full rounded-lg border-pb-border"></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Start date') }}</label><input type="date" name="start_date" class="mt-1 w-full rounded-lg border-pb-border"></div>
          <div><label class="block text-sm font-medium">{{ __('End date') }}</label><input type="date" name="end_date" class="mt-1 w-full rounded-lg border-pb-border"></div>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Location') }}</label><input type="text" name="location" class="mt-1 w-full rounded-lg border-pb-border"></div>
        <div><label class="block text-sm font-medium">{{ __('Description') }}</label><textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-pb-border"></textarea></div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-pb-navy text-white font-semibold text-sm">{{ __('Create') }}</button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
