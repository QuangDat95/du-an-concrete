<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class OrganizationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'name' => ['required',Rule::unique('organizations')->ignore($this->name_old,'name')],
                'address' => ['required',Rule::unique('organizations')->ignore($this->address_company_old,'address')],
                'tax_number' => ['required',Rule::unique('organizations')->ignore($this->tax_old,'tax_number')],
                'email' => ['email:rfc,dns',Rule::unique('organizations')->ignore($this->email_old,'email')]
            ];
        }
        return [];
    }
}
