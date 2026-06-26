<div class="pb-field">
  <label for="player-name">{{ __('Name') }}</label>
  <input type="text" id="player-name" name="name" value="{{ old('name', $player->name) }}" required />
</div>
<div class="pb-field">
  <label for="player-program">{{ __('Program') }}</label>
  <select id="player-program" name="program_id">
    <option value="">{{ __('None') }}</option>
    @foreach ($programs as $program)
      <option value="{{ $program->id }}" @selected(old('program_id', $player->program_id) == $program->id)>{{ $program->name }}</option>
    @endforeach
  </select>
</div>
<div class="pb-field">
  <label for="player-season">{{ __('Season') }}</label>
  <select id="player-season" name="season_id">
    <option value="">{{ __('None') }}</option>
    @foreach ($seasons as $season)
      <option value="{{ $season->id }}" @selected(old('season_id', $player->season_id) == $season->id)>{{ $season->name }}</option>
    @endforeach
  </select>
</div>
<div class="pb-field">
  <label for="player-status">{{ __('Status') }}</label>
  <input type="text" id="player-status" name="status" value="{{ old('status', $player->status ?? 'active') }}" />
</div>
<div class="pb-field">
  <label for="player-dob">{{ __('Date of birth') }}</label>
  <input type="date" id="player-dob" name="date_of_birth" value="{{ old('date_of_birth', $player->date_of_birth?->format('Y-m-d')) }}" />
</div>
<div class="pb-field">
  <label for="player-position">{{ __('Primary position') }}</label>
  <input type="text" id="player-position" name="primary_position" value="{{ old('primary_position', $player->primary_position) }}" />
</div>
