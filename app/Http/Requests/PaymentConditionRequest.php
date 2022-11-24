<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PaymentConditionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'name' => ['required',Rule::unique('payment_conditions')->ignore($this->name_old,'name')],
                'date_owned' => 'required'
            ];
        }
        return [];
    }
}