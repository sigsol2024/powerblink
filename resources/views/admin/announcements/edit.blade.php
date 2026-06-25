<x-app-layout>
  <x-admin.page-header :title="__('Edit announcement')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-4 max-w-xl">
        @csrf @method('PUT')
        <div>
          <label class="block text-sm font-medium">{{ __('Season') }}</label>
          <select name="season_id" class="mt-1 w-full rounded-lg border-pb-border">
            <option value="">{{ __('All seasons') }}</option>
            @foreach ($seasons as $season)<option value="{{ $season->id }}" @selected($announcement->season_id == $season->id)>{{ $season->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title" value="{{ old('title', $announcement->title) }}" class="mt-1 w-full rounded-lg border-pb-border" required></div>
        <div><label class="block text-sm font-medium">{{ __('Body') }}</label><textarea name="body" rows="6" class="mt-1 w-full rounded-lg border-pb-border" required>{{ old('body', $announcement->body) }}</textarea></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Audience') }}</label><input type="text" name="audience" value="{{ old('audience', $announcement->audience) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
          <div><label class="block text-sm font-medium">{{ __('Channel') }}</label><input type="text" name="channel" value="{{ old('channel', $announcement->channel) }}" class="mt-1 w-full rounded-lg border-pb-border"></div>
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-pb-navy text-white font-semibold text-sm">{{ __('Save') }}</button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
