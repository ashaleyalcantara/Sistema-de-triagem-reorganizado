<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'senha' => ['required'],
            'perfil' => ['required', 'in:administrador,recepcionista,enfermeiro,medico'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'senha.required' => 'A senha é obrigatória.',
            'perfil.required' => 'Selecione um perfil.',
            'perfil.in' => 'Perfil inválido.',
        ];
    }
}
