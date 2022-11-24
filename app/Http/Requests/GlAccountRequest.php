<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class GlAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'account_code' => ['required',Rule::unique('gl_accounts')->ignore($this->account_code_old,'account_code')],
                'name' => 'required',
                'nature_id' => 'required'
            ];
        }
        return [];
    }
}
