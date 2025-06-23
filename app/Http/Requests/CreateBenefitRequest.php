<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBenefitRequest extends FormRequest
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
            'name' => 'required',
            'benefitDetailFormGroup' => 'required',
            'filePoliticas' => 'required_unless:filePoliticas,null',
            'logo_file' => 'required_unless:logo_file,null',
            'valid_id' => 'required'
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'valid_id' => $this->toBoolean($this->valid_id),
        ]);
    }

    /**
     * Convert to boolean
     *
     * @param $booleable
     * @return boolean
     */
    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
