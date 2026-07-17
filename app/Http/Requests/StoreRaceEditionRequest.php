<?php

namespace App\Http\Requests;

use App\Models\Race;
use App\Models\RaceEdition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRaceEditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_domestic'    => $this->boolean('is_domestic', true),
            'is_active'      => $this->boolean('is_active', true),
            'is_review_open' => $this->boolean('is_review_open', false),
            'is_published'   => $this->boolean('is_published', false),
            'country'     => $this->input('country') ?: ($this->boolean('is_domestic', true) ? '대한민국' : null),
            'year'        => $this->input('year') ?: ($this->input('race_date') ? date('Y', strtotime($this->input('race_date'))) : null),
        ]);

        $cats = $this->input('categories', []);
        if (! is_array($cats)) {
            return;
        }

        $filtered = array_values(array_filter($cats, function ($cat) {
            return filled($cat['name'] ?? null)
                || filled($cat['distance_km'] ?? null)
                || filled($cat['entry_fee'] ?? null);
        }));

        $this->merge(['categories' => $filtered]);
    }

    public function rules(): array
    {
        $edition = $this->route('race_edition');
        $editionId = $edition instanceof RaceEdition ? $edition->id : null;

        return [
            'race_id'        => ['required', 'integer', Rule::exists(Race::class, 'id')],
            'name'           => 'required|string|max:200',
            'year'           => [
                'required',
                'integer',
                'min:1990',
                'max:2100',
                Rule::unique(RaceEdition::class, 'year')
                    ->where(fn ($q) => $q->where('race_id', $this->input('race_id')))
                    ->ignore($editionId),
            ],
            'race_date'      => 'nullable|date',
            'race_time'      => 'nullable|string|max:20',
            'location'       => 'nullable|string|max:200',
            'city'           => 'nullable|string|max:100',
            'is_domestic'    => 'boolean',
            'country'        => 'nullable|string|max:50',
            'source'         => 'nullable|string|max:50',
            'source_url'     => 'nullable|url|max:500',
            'weather_stn_id' => 'nullable|integer|min:1',
            'entry_fee'      => 'nullable|string|max:100',
            'reg_start'      => 'nullable|date',
            'reg_end'        => 'nullable|date|after_or_equal:reg_start',
            'status'         => 'nullable|string|in:upcoming,ended,접수전,접수중,접수마감,대회종료,active',
            'is_active'      => 'boolean',
            'is_review_open' => 'boolean',
            'is_published'   => 'boolean',
            'organizer'      => 'nullable|string|max:255',
            'website_url'    => 'nullable|url|max:500',
            'categories'                 => ['nullable', 'array'],
            'categories.*.name'          => ['required', 'string', 'max:100'],
            'categories.*.distance_km'   => ['required', 'numeric', 'min:0', 'max:999.999'],
            'categories.*.entry_fee'     => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'race_id.required'       => '대회(마스터)를 선택해 주세요.',
            'race_id.exists'         => '선택한 대회가 존재하지 않습니다.',
            'name.required'          => '대회명은 필수입니다.',
            'year.required'          => '개최 연도는 필수입니다.',
            'year.min'               => '1990년 이후 대회만 등록 가능합니다.',
            'year.unique'            => '해당 대회의 같은 연도 데이터가 이미 있습니다.',
            'reg_end.after_or_equal' => '접수 종료일은 시작일 이후여야 합니다.',
            'source_url.url'         => '올바른 URL 형식을 입력해주세요.',
            'website_url.url'        => '올바른 URL 형식을 입력해주세요.',
            'categories.*.name.required'        => '종목 이름을 입력해 주세요.',
            'categories.*.distance_km.required' => '거리(km)를 입력해 주세요.',
            'categories.*.distance_km.numeric'  => '거리는 숫자로 입력해 주세요. (예: 5.5)',
            'categories.*.entry_fee.required'   => '참가비를 입력해 주세요.',
            'categories.*.entry_fee.integer'    => '참가비는 숫자(원)로 입력해 주세요.',
        ];
    }
}
