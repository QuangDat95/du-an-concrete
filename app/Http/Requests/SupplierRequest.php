<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SupplierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'code' => ['required',Rule::unique('suppliers')->ignore($this->code_old,'code')],
                'name' => 'required',
                'address' => 'required'
            ];
        }
        return [];
    }
}
