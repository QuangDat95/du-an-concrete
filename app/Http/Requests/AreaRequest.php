<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class AreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'name' => ['required',Rule::unique('areas')->ignore($this->name_old,'name')],
            ];
        }
        return [];
    }
}
