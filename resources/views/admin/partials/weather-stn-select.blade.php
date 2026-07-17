@php
    $selected = old('weather_stn_id', $selectedStnId ?? null);
    $stations = $weatherStations ?? collect();
@endphp
<div class="adm-field">
    <label class="adm-label">기상청 지점 <span class="adm-label-hint">(비워두면 자동 추론)</span></label>
    <select name="weather_stn_id" class="adm-input">
        <option value="">자동 추론</option>
        @foreach($stations as $stn)
            <option value="{{ $stn->stn_id }}" @selected((string) $selected === (string) $stn->stn_id)>
                {{ $stn->stn_name }} ({{ $stn->stn_id }})
            </option>
        @endforeach
    </select>
    @error('weather_stn_id')<p class="adm-field-error">{{ $message }}</p>@enderror
</div>
