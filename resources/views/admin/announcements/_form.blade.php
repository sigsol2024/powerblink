<div><label class="block text-sm font-medium mb-1">{{ __('Title') }}</label><input name="title" value="{{ old('title', $announcement->title) }}" required class="w-full rounded-lg border-outline-variant" /></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Body') }}</label><textarea name="body" rows="6" required class="w-full rounded-lg border-outline-variant">{{ old('body', $announcement->body) }}</textarea></div>
<div class="grid grid-cols-2 gap-3">
  <div><label class="block text-sm font-medium mb-1">{{ __('Audience') }}</label><input name="audience" value="{{ old('audience', $announcement->audience) }}" class="w-full rounded-lg border-outline-variant" /></div>
  <div><label class="block text-sm font-medium mb-1">{{ __('Channel') }}</label><input name="channel" value="{{ old('channel', $announcement->channel) }}" class="w-full rounded-lg border-outline-variant" /></div>
</div>
<div><label class="block text-sm font-medium mb-1">{{ __('Published at') }}</label><input type="datetime-local" name="published_at" value="{{ old('published_at', $announcement->published_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-outline-variant" /></div>
