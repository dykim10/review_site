<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'distance' => 'required|string|max:20',
            'rating'   => 'required|integer|min:1|max:5',
            'content'  => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'distance.required' => '참가 거리를 선택해주세요.',
            'rating.required'   => '평점을 선택해주세요.',
            'content.required'  => '리뷰 내용을 입력해주세요.',
            'content.min'       => '리뷰는 최소 10자 이상 작성해주세요.',
            'content.max'       => '리뷰는 2000자 이하로 작성해주세요.',
        ];
    }
}
