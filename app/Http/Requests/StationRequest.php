<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'name' => ['required',Rule::unique('organizations')->ignore($this->name_old,'name')],
                'area_id' => 'required',
                'parent_id' => 'required'
            ];
        }
        return [];
    }
}
