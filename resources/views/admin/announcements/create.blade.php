<x-app-layout>
  <x-admin.page-header :title="__('New announcement')" />
  <x-admin.page-content>
    <x-admin.card>
      <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-4 max-w-xl">
        @csrf
        <div>
          <label class="block text-sm font-medium">{{ __('Season') }}</label>
          <select name="season_id" class="mt-1 w-full rounded-lg border-pb-border">
            <option value="">{{ __('All seasons') }}</option>
            @foreach ($seasons as $season)<option value="{{ $season->id }}">{{ $season->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="block text-sm font-medium">{{ __('Title') }}</label><input type="text" name="title" class="mt-1 w-full rounded-lg border-pb-border" required></div>
        <div><label class="block text-sm font-medium">{{ __('Body') }}</label><textarea name="body" rows="6" class="mt-1 w-full rounded-lg border-pb-border" required></textarea></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium">{{ __('Audience') }}</label><input type="text" name="audience" value="all" class="mt-1 w-full rounded-lg border-pb-border"></div>
          <div><label class="block text-sm font-medium">{{ __('Channel') }}</label><input type="text" name="channel" value="in_app" class="mt-1 w-full rounded-lg border-pb-border"></div>
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-pb-navy text-white font-semibold text-sm">{{ __('Publish') }}</button>
      </form>
    </x-admin.card>
  </x-admin.page-content>
</x-app-layout>
