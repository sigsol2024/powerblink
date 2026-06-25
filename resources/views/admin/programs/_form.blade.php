<div><label class="block text-sm font-medium mb-1">{{ __('Name') }}</label><input name="name" value="{{ old('name', $program->name) }}" required class="w-full rounded-lg border-outline-variant" /></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Age group') }}</label><input name="age_group" value="{{ old('age_group', $program->age_group) }}" class="w-full rounded-lg border-outline-variant" /></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Season') }}</label>
  <select name="season_id" class="w-full rounded-lg border-outline-variant"><option value="">{{ __('None') }}</option>
    @foreach ($seasons as $season)<option value="{{ $season->id }}" @selected(old('season_id', $program->season_id) == $season->id)>{{ $season->name }}</option>@endforeach
  </select></div>
<div><label class="block text-sm font-medium mb-1">{{ __('Description') }}</label><textarea name="description" rows="4" class="w-full rounded-lg border-outline-variant">{{ old('description', $program->description) }}</textarea></div>
<div class="grid grid-cols-2 gap-3">
  <div><label class="block text-sm font-medium mb-1">{{ __('Registration fee') }}</label><input type="number" name="registration_fee" value="{{ old('registration_fee', $program->registration_fee) }}" class="w-full rounded-lg border-outline-variant" /></div>
  <div><label class="block text-sm font-medium mb-1">{{ __('Monthly fee') }}</label><input type="number" name="monthly_fee" value="{{ old('monthly_fee', $program->monthly_fee) }}" class="w-full rounded-lg border-outline-variant" /></div>
</div>
<div><label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $program->is_active ?? true)) /> {{ __('Active') }}</label></div>
