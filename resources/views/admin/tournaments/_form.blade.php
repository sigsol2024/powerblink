<div><label class="block text-sm font-medium mb-1">{{ __('Title') }}</label><input name="title" value="{{ old('title', $tournament->title) }}" required class="w-full rounded-lg border-outline-variant" /></div>
<div class="grid grid-cols-2 gap-3">
  <div><label class="block text-sm font-medium mb-1">{{ __('Start date') }}</label><input type="date" name="start_date" value="{{ old('start_date', $tournament->start_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-outline-variant" /></div>
  <div><label class="block text-sm font-medium mb-1">{{ __('End date') }}</label><input type="date" name="end_date" value="{{ old('end_date', $tournament->end_date?->format('Y-m-d')) }}" class="w-full rounded-lg border-outline-variant" /></div>
</div>
<div><label class="block text-sm font-medium mb-1">{{ __('Location') }}</label><input name="location" value="{{ old('location', $tournament->location) }}" class="w-full rounded-lg border-outline-variant" /></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Description') }}</label><textarea name="description" rows="3" class="w-full rounded-lg border-outline-variant">{{ old('description', $tournament->description) }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Status') }}</label><input name="status" value="{{ old('status', $tournament->status) }}" class="w-full rounded-lg border-outline-variant" /></div>
