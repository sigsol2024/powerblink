<x-app-layout>
  <x-admin.page-header :title="__('Edit tournament')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.tournaments.update', $tournament) }}" class="space-y-4 max-w-xl">
        @csrf @method('PUT')
        <div>
          <label class="block text-sm font-medium">{{ __('Season') }}</label>
          <select name="season_id" class="mt-1 w-full rounded-lg border-pb-border" required>
            @foreach ($seasons as $season)<option value="{{ $season->id }}" @selected($tournament->season_id == $season->id)>{{ $season->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title" value="{{ old('title', $tournament->title) }}" class="mt-1 w-full rounded-lg border-pb-border" required></div>
        <div><label class="block text-sm font-medium">{{ __('Category') }}</label><input type="text" name="category" value="{{ old('category', $tournament->category) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Start date') }}</label><input type="date" name="start_date" value="{{ old('start_date', $tournament->start_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
          <div><label class="block text-sm font-medium">{{ __('End date') }}</label><input type="date" name="end_date" value="{{ old('end_date', $tournament->end_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Location') }}</label><input type="text" name="location" value="{{ old('location', $tournament->location) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
        <div><label class="block text-sm font-medium">{{ __('Description') }}</label><textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-pb-border">{{ old('description', $tournament->description) }}</textarea></div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-pb-navy text-white font-semibold text-sm">{{ __('Save') }}</button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
