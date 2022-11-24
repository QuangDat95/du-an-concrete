<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'payment_date' => 'required',
                'created_by' => 'required',
                'transaction_type_id' => 'required',
                'code' => 'required',
                'company_id' => 'required'
            ];
        }
        return [];
    }
}
