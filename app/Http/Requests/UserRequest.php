<?php

namespace App\Http\Requests;

use App\Models\User;
// use App\Rules\CheckLinkUserRule;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required|min:3',Rule::unique('users')->ignore($this->name_old,'name')],
            'email' => [
                'required', 'email'
            ],
            'password' => [
                'required'
            ]
        ];
    }

    // protected function getRedirectUrl()
    // {
    //     $url = $this->redirector->getUrlGenerator();
    //     if ($this->redirect) {
    //         return $url->to($this->redirect);
    //     } elseif ($this->redirectRoute) {
    //         return $url->route($this->redirectRoute);
    //     } elseif ($this->redirectAction) {
    //         return $url->action($this->redirectAction);
    //     }
    //     return $url->previous();
    // }
}
