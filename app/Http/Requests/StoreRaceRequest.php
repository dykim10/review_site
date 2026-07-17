<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['super_admin', 'crew_admin']);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->boolean('is_published', false),
            'is_domestic'  => $this->has('is_domestic')
                ? $this->boolean('is_domestic')
                : true,
            'country' => $this->input('country')
                ?: ($this->boolean('is_domestic', true) ? '대한민국' : null),
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
        return [
            'name'           => 'required|string|max:255',
            'year'           => 'nullable|integer|min:1990|max:2100',
            'race_date'      => 'nullable|date',
            'race_time'      => 'nullable|string|max:20',
            'location'       => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'organizer'      => 'nullable|string|max:255',
            'entry_fee'      => 'nullable|integer|min:0',
            'website_url'    => 'nullable|url|max:500',
            'reg_start'      => 'nullable|date',
            'reg_end'        => 'nullable|date|after_or_equal:reg_start',
            'status'         => 'nullable|string|in:접수전,접수중,접수마감,대회종료,upcoming,ended',
            'weather_stn_id' => 'nullable|integer|min:1',
            'is_published'   => 'boolean',
            'is_domestic'    => 'boolean',
            'country'        => 'nullable|string|max:50',
            'distances_raw'  => 'nullable|string|max:500',
            'categories'                 => ['nullable', 'array'],
            'categories.*.name'          => ['required', 'string', 'max:100'],
            'categories.*.distance_km'   => ['required', 'numeric', 'min:0', 'max:999.999'],
            'categories.*.entry_fee'     => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => '대회명은 필수입니다.',
            'reg_end.after_or_equal' => '접수 종료일은 시작일 이후여야 합니다.',
            'website_url.url'        => '올바른 URL 형식을 입력해주세요.',
            'categories.*.name.required'        => '종목 이름을 입력해 주세요.',
            'categories.*.distance_km.required' => '거리(km)를 입력해 주세요.',
            'categories.*.distance_km.numeric'  => '거리는 숫자로 입력해 주세요. (예: 5.5)',
            'categories.*.entry_fee.required'   => '참가비를 입력해 주세요.',
            'categories.*.entry_fee.integer'    => '참가비는 숫자(원)로 입력해 주세요.',
        ];
    }
}
