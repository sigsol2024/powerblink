<div><label class="block text-sm font-medium mb-1">{{ __('Name') }}</label><input name="name" value="{{ old('name', $coach->name) }}" required class="w-full rounded-lg border-outline-variant" /></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Title') }}</label><input name="title" value="{{ old('title', $coach->title) }}" class="w-full rounded-lg border-outline-variant" /></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Bio') }}</label><textarea name="bio" rows="4" class="w-full rounded-lg border-outline-variant">{{ old('bio', $coach->bio) }}</textarea></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Specialization') }}</label><input name="specialization" value="{{ old('specialization', $coach->specialization) }}" class="w-full rounded-lg border-outline-variant" /></div>
<div class="grid grid-cols-2 gap-3">
  <div><label class="block text-sm font-medium mb-1">{{ __('License') }}</label><input name="license_level" value="{{ old('license_level', $coach->license_level) }}" class="w-full rounded-lg border-outline-variant" /></div>
  <div><label class="block text-sm font-medium mb-1">{{ __('Experience (years)') }}</label><input type="number" name="experience_years" value="{{ old('experience_years', $coach->experience_years) }}" class="w-full rounded-lg border-outline-variant" /></div>
</div>
<div class="grid grid-cols-2 gap-3">
  <div><label class="block text-sm font-medium mb-1">{{ __('Email') }}</label><input type="email" name="email" value="{{ old('email', $coach->email) }}" class="w-full rounded-lg border-outline-variant" /></div>
  <div><label class="block text-sm font-medium mb-1">{{ __('Phone') }}</label><input name="phone" value="{{ old('phone', $coach->phone) }}" class="w-full rounded-lg border-outline-variant" /></div>
</div>
