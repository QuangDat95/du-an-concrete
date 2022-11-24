<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class BankAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'account_name' => ['required',Rule::unique('bank_accounts')->ignore($this->name_old,'account_name')],
                'account_number' => ['required',Rule::unique('bank_accounts')->ignore($this->account_number_old,'account_number')],
                'bank' => ['required',Rule::unique('bank_accounts')->ignore($this->bank_old,'bank')]
            ];
        }
        return [];
    }
}
