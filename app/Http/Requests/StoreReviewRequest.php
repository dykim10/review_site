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
            'race_edition_id'    => 'nullable|integer|exists:review.race_editions,id',
            'distance'           => 'required|string|max:20',
            'rating'             => 'required|integer|min:1|max:5',
            'content'            => 'required|string|min:10|max:2000',
            'images'             => 'nullable|array|max:10',
            'images.*'           => 'file|image|max:10240',
            'existing_images'    => 'nullable|array|max:10',
            'existing_images.*'  => 'nullable|string|max:500',
            'hashtags'           => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'distance.required'  => '참가 거리를 선택해주세요.',
            'rating.required'    => '평점을 선택해주세요.',
            'content.required'   => '리뷰 내용을 입력해주세요.',
            'content.min'        => '리뷰는 최소 10자 이상 작성해주세요.',
            'content.max'        => '리뷰는 2000자 이하로 작성해주세요.',
            'images.max'         => '이미지는 최대 10장까지 업로드할 수 있습니다.',
            'images.*.image'     => '이미지 파일만 업로드 가능합니다.',
            'images.*.max'       => '이미지 파일당 최대 10MB까지 업로드 가능합니다.',
        ];
    }
}
