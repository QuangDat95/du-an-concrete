<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class TransactionEntryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'station_id' => ['required',Rule::unique('transaction_entries')->ignore($this->station_old,'station_id')]
            ];
        }
        return [];
    }
}
