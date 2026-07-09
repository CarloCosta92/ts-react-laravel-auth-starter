<?php

namespace App\Http\Requests;

class RegisterRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            "name" => "required|string",
            "email" => "required|string|email|unique:users",
            "password" => "required|string|min:8|confirmed"
          
        ];
    }

    public function messages(): array
{
    return [
        "name.required" => "Il nome è obbligatorio",
        "email.required" => "Email obbligatoria",
        "email.email" => "Inserisci un indirizzo email valido",
        "email.unique" => "Mail già esistente",
        "password.required" => "La password è obbligatoria",
        "password.min" => "Deve avere almeno :min caratteri",
        "password.confirmed"=>"La password non coincide"
    ];
}
}