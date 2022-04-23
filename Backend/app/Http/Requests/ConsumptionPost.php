<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsumptionPost extends FormRequest
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


    public function rules()
    {
        return [
            'observation_id' => 'nullable|integer',
            'value' => 'required|numeric',
            'variance' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'observation_id.integer' => 'Observation id must be a number',
            'value.required' => 'Value is mandatory',
            'value.numeric' => 'Value must be a number',
            'variance.required' => 'Variance is mandatory',
            'variance.numeric' => 'Variance must be a number',
        ];
    }
}
