<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRaceEditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'race_id'        => 'nullable|integer|exists:review.races,id',
            'name'           => 'required|string|max:200',
            'year'           => 'required|integer|min:1990|max:2100',
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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => '대회명은 필수입니다.',
            'year.required'          => '개최 연도는 필수입니다.',
            'year.min'               => '1990년 이후 대회만 등록 가능합니다.',
            'reg_end.after_or_equal' => '접수 종료일은 시작일 이후여야 합니다.',
            'source_url.url'         => '올바른 URL 형식을 입력해주세요.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_domestic'    => $this->boolean('is_domestic', true),
            'is_active'      => $this->boolean('is_active', true),
            'is_review_open' => $this->boolean('is_review_open', false),
            'country'     => $this->input('country') ?: ($this->boolean('is_domestic', true) ? '대한민국' : null),
            'year'        => $this->input('year') ?: ($this->input('race_date') ? date('Y', strtotime($this->input('race_date'))) : null),
        ]);
    }
}
