<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ContractRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'contract_code' => ['required',Rule::unique('contracts')->ignore($this->name_old,'contract_code')],
                'customer_id' => 'required',
                'construction_id' => 'required',
                'contract_date' => 'required',
                'due_date' => 'required',
                'payment_condition_id' => 'required'
            ];
        }
        return [];
    }
}
