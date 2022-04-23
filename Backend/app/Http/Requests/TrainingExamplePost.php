<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingExamplePost extends FormRequest
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
            'consumptions' => 'nullable|array',
            'consumption_start' => 'nullable|integer',
            'consumption_end' => 'nullable|integer',
            'equipments_on' => 'nullable|array'
        ];
    }
}
