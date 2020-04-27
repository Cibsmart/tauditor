<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeductionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'deduction_type' => ['required', 'integer'],
            'deduction_name' => ['required', 'integer'],
            'value_type'     => ['required', 'string'],
            'value'          => 'exclude_if:value_type,1|numeric|positive',
            'new_deduction'  => 'exclude_unless:deduction_name,-1|required|string|min:3',
        ];
    }
}
