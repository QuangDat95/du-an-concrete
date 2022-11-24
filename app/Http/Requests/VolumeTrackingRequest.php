<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VolumeTrackingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->method() == 'POST'){
            return [
                'user_id' => 'required',
                'customer_id' => 'required',
                'construction_id' => 'required',
                'actual_weight' => 'required',
                'payment_volume' => 'required',
                'concreate_price' => 'required',
                'station_id' => 'required',
                'area_id' => 'required'
            ];
        }
        return [];
    }
}
