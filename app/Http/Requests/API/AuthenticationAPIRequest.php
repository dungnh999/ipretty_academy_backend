<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationAPIRequest extends APIRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
      'password' => 'required',
    ];
  }

  public function messages()
  {
    return [
      'email.required' => __('messages.email_required'),
      'email.regex' => __('messages.email_regex'),
      'password.required' => __('messages.password_required'),
      'password.min' => __('messages.password_min'),
    ];
  }
}
