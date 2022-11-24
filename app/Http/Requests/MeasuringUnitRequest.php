<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeasuringUnitRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'code' => 'required',
                'name' => 'required'
            ];
        }
        return [];
    }
}
