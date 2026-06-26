<x-app-layout>
  <x-admin.page-header :title="__('Edit tournament')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.tournaments.update', $tournament) }}" class="pb-admin-form">
        @csrf @method('PUT')
        <div>
          <label class="block text-sm font-medium">{{ __('Season') }}</label>
          <select name="season_id"  required>
            @foreach ($seasons as $season)<option value="{{ $season->id }}" @selected($tournament->season_id == $season->id)>{{ $season->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title" value="{{ old('title', $tournament->title) }}"  required></div>
        <div><label class="block text-sm font-medium">{{ __('Category') }}</label><input type="text" name="category" value="{{ old('category', $tournament->category) }}" ></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Start date') }}</label><input type="date" name="start_date" value="{{ old('start_date', $tournament->start_date?->format('Y-m-d')) }}" ></div>
          <div><label class="block text-sm font-medium">{{ __('End date') }}</label><input type="date" name="end_date" value="{{ old('end_date', $tournament->end_date?->format('Y-m-d')) }}" ></div>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Location') }}</label><input type="text" name="location" value="{{ old('location', $tournament->location) }}" ></div>
        <div><label class="block text-sm font-medium">{{ __('Description') }}</label><textarea name="description" rows="4" >{{ old('description', $tournament->description) }}</textarea></div>
        <x-admin.button type="submit">{{ __('Save') }}</x-admin.button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
