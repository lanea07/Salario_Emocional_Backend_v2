<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBenefitUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'benefit_id' => 'required',
            'benefit_detail_id' => 'required',
            'user_id' => 'required',
            'benefit_begin_time' => 'required',
            'benefit_end_time' => 'required',
            'request_comment' => 'nullable',
            'decision_comment' => 'nullable',
        ];
    }
}
