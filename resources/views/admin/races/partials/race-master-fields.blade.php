@if($race)
    <div style="border-top:1px solid #E5E7EB;margin:1.5rem 0;padding-top:1.5rem;">
        <div style="font-weight:700;font-size:0.9rem;margin-bottom:0.35rem;">
            대회 카탈로그
            <span class="adm-label-hint">races #{{ $race->id }} — 연도 공통 정보</span>
        </div>
        <p style="margin:0 0 1rem;font-size:0.78rem;color:#6B7280;line-height:1.5;">
            주최·홈페이지·거리는 카탈로그(races)에 저장됩니다. 같은 대회의 다른 연도 edition과 공유됩니다.
        </p>

        <div class="adm-grid-2">
            <div class="adm-field">
                <label class="adm-label">주최자</label>
                <input type="text" name="organizer" value="{{ old('organizer', $race->organizer) }}" class="adm-input">
            </div>
            <div class="adm-field">
                <label class="adm-label">공식 홈페이지</label>
                <input type="url" name="website_url" value="{{ old('website_url', $race->website_url) }}" class="adm-input">
                @error('website_url')<p class="adm-field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="adm-field">
            <label class="adm-label">거리 <span class="adm-label-hint">(쉼표로 구분)</span></label>
            <input type="text" name="distances_raw"
                   value="{{ old('distances_raw', is_array($race->distances) ? implode(', ', $race->distances) : $race->distances) }}"
                   class="adm-input">
        </div>
    </div>
@endif
