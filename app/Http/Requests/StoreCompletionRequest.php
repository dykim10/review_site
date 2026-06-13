<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompletionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'race_edition_id' => 'required|integer|exists:review.race_editions,id',
            'distance_km'     => 'nullable|numeric|min:1|max:250',
            'finish_time'     => ['nullable', 'string', 'max:10', 'regex:/^\d+:\d{2}(:\d{2})?$/'],
            'official_time'   => ['nullable', 'string', 'max:10', 'regex:/^\d+:\d{2}(:\d{2})?$/'],
            'bib_number'      => 'nullable|string|max:20',
            'certificate'     => 'nullable|file|image|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'race_edition_id.required' => '대회 회차를 선택해주세요.',
            'race_edition_id.exists'   => '유효하지 않은 대회 회차입니다.',
            'finish_time.regex'        => '시간 형식이 올바르지 않습니다. 예: 3:45:30',
            'official_time.regex'      => '시간 형식이 올바르지 않습니다. 예: 3:45:30',
            'certificate.image'        => '이미지 파일만 업로드 가능합니다.',
            'certificate.max'          => '파일 크기는 10MB 이하여야 합니다.',
        ];
    }
}
