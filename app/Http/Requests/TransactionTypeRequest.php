<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class TransactionTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'name' => ['required',Rule::unique('transaction_types')->ignore($this->name_old,'name')],
                'payment_method_id' => 'required',
                'debit_account_id' =>'nullable|different:credit_account_id',
                'credit_account_id' => 'nullable|different:debit_account_id'
            ];
        }
        return [];
    }

    public function messages()
    {
        return [
            'debit_account_id.different' => 'Tài khoản ghi nợ phải khác tài khoản ghi có',
            'credit_account_id.different' => 'Tài khoản ghi có phải khác tài khoản ghi nợ'
        ];
    }
}