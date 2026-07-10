<?php

namespace App\Http\Requests;


class ForgotPasswordRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
           "email" => "required|email",
          
        ];
    }

     public function messages(): array
    {
        return [
            "email.required" => "Email obbligatoria",
            "email.email" => "Inserisci un indirizzo email valido",
        ];
    }
}