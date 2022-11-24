<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'name' => ['required',Rule::unique('customers')->ignore($this->name_old,'name')],
                'name_other' => ['required'],
                'type_id' => ['required']
            ];
        }
        return [];
    }
}
