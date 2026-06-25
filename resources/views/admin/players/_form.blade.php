<div>
  <label class="block text-sm font-medium mb-1">{{ __('Name') }}</label>
  <input type="text" name="name" value="{{ old('name', $player->name) }}" required class="w-full rounded-lg border-outline-variant" />
</div>
<div>
  <label class="block text-sm font-medium mb-1">{{ __('Program') }}</label>
  <select name="program_id" class="w-full rounded-lg border-outline-variant">
    <option value="">{{ __('None') }}</option>
    @foreach ($programs as $program)
      <option value="{{ $program->id }}" @selected(old('program_id', $player->program_id) == $program->id)>{{ $program->name }}</option>
    @endforeach
  </select>
</div>
<div>
  <label class="block text-sm font-medium mb-1">{{ __('Season') }}</label>
  <select name="season_id" class="w-full rounded-lg border-outline-variant">
    <option value="">{{ __('None') }}</option>
    @foreach ($seasons as $season)
      <option value="{{ $season->id }}" @selected(old('season_id', $player->season_id) == $season->id)>{{ $season->name }}</option>
    @endforeach
  </select>
</div>
<div>
  <label class="block text-sm font-medium mb-1">{{ __('Status') }}</label>
  <input type="text" name="status" value="{{ old('status', $player->status ?? 'active') }}" class="w-full rounded-lg border-outline-variant" />
</div>
<div>
  <label class="block text-sm font-medium mb-1">{{ __('Date of birth') }}</label>
  <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $player->date_of_birth?->format('Y-m-d')) }}" class="w-full rounded-lg border-outline-variant" />
</div>
<div>
  <label class="block text-sm font-medium mb-1">{{ __('Primary position') }}</label>
  <input type="text" name="primary_position" value="{{ old('primary_position', $player->primary_position) }}" class="w-full rounded-lg border-outline-variant" />
</div>
