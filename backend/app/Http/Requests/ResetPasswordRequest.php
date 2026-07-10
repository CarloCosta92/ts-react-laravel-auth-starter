<?php

namespace App\Http\Requests;


class ResetPasswordRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
           "email" => "required|string|email",
           "password" => "required|string|min:8|confirmed",
           "token" => "required|string",
          
        ];
    }

     public function messages(): array
    {
        return [
        "email.required" => "Email obbligatoria",
        "email.email" => "Inserisci un indirizzo email valido",
        "password.required" => "La password è obbligatoria",
        "password.min" => "Deve avere almeno :min caratteri",
        "password.confirmed"=>"La password non coincide"
        ];
    }
}