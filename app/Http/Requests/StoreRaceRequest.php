<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['super_admin', 'crew_admin']);
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'race_date'   => 'required|date',
            'race_time'   => 'nullable|string|max:20',
            'location'    => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'organizer'   => 'nullable|string|max:255',
            'entry_fee'   => 'nullable|integer|min:0',
            'website_url' => 'nullable|url|max:500',
            'reg_start'   => 'nullable|date',
            'reg_end'     => 'nullable|date|after_or_equal:reg_start',
            'status'         => 'nullable|string|in:접수전,접수중,접수마감,대회종료',
            'weather_stn_id' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => '대회명은 필수입니다.',
            'race_date.required' => '대회일은 필수입니다.',
            'reg_end.after_or_equal' => '접수 종료일은 시작일 이후여야 합니다.',
            'website_url.url'  => '올바른 URL 형식을 입력해주세요.',
        ];
    }
}
