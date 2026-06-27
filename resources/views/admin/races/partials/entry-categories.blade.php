@php
    $initialRows = old('categories');
    if ($initialRows === null && isset($edition) && $edition) {
        $initialRows = $edition->entryCategories->map(fn ($c) => [
            'name'        => $c->name,
            'distance_km' => $c->distance_km,
            'entry_fee'   => $c->entry_fee,
        ])->values()->all();
    }
@endphp

@once
    @push('scripts')
    <script>
    window.admEntryCategories = function(initial) {
        return {
            rows: (initial && initial.length)
                ? initial.map(function(r) {
                    return {
                        name: r.name ?? '',
                        distance_km: r.distance_km ?? '',
                        entry_fee: r.entry_fee ?? '',
                    };
                })
                : [{ name: '', distance_km: '', entry_fee: '' }],
            add: function() {
                this.rows.push({ name: '', distance_km: '', entry_fee: '' });
            },
            remove: function(i) {
                this.rows.splice(i, 1);
                if (!this.rows.length) {
                    this.add();
                }
            },
        };
    };
    </script>
    @endpush
@endonce

<div class="adm-field" x-data="admEntryCategories(@js($initialRows ?? []))">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.65rem;">
        <label class="adm-label" style="margin:0;">참가 종목 / 참가비</label>
        <button type="button" @click="add()" class="adm-btn adm-btn-ghost" style="padding:0.35rem 0.75rem;font-size:0.78rem;">+ 종목 추가</button>
    </div>
    <p style="font-size:0.72rem;color:#9CA3AF;margin-bottom:0.75rem;">
        종목마다 거리(km)·참가비를 입력합니다. 거리는 소수 가능 (예: 42.195, 5.5).
    </p>

    <template x-for="(row, idx) in rows" :key="idx">
        <div class="adm-grid-2" style="grid-template-columns:2fr 1fr 1fr auto;gap:0.5rem;margin-bottom:0.5rem;align-items:start;">
            <input type="text"
                   :name="'categories[' + idx + '][name]'"
                   x-model="row.name"
                   placeholder="예: 풀코스 / 50K 울트라"
                   class="adm-input">
            <input type="number"
                   step="0.001"
                   min="0"
                   :name="'categories[' + idx + '][distance_km]'"
                   x-model="row.distance_km"
                   placeholder="거리 km"
                   class="adm-input">
            <input type="number"
                   step="1"
                   min="0"
                   :name="'categories[' + idx + '][entry_fee]'"
                   x-model="row.entry_fee"
                   placeholder="참가비(원)"
                   class="adm-input">
            <button type="button"
                    @click="remove(idx)"
                    class="adm-btn adm-btn-ghost"
                    style="padding:0.45rem 0.6rem;font-size:0.75rem;color:#DC2626;">삭제</button>
        </div>
    </template>

    @error('categories.*.name')
        <p class="adm-field-error">{{ $message }}</p>
    @enderror
    @error('categories.*.distance_km')
        <p class="adm-field-error">{{ $message }}</p>
    @enderror
    @error('categories.*.entry_fee')
        <p class="adm-field-error">{{ $message }}</p>
    @enderror
</div>
