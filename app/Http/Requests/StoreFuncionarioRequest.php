<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuncionarioRequest extends FormRequest
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
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:funcionarios,email'],
            'cpf' => ['required', 'string', 'size:11', 'unique:funcionarios,cpf'],
            'senha' => ['required', 'string', 'min:6'],
            'perfil' => ['required', 'in:Administrador,Recepcionista,Enfermeiro,Médico'],
            'contato' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado! Tente outro.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve conter exatamente 11 números.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'perfil.required' => 'Selecione um perfil.',
            'perfil.in' => 'Perfil inválido.',
            'contato.max' => 'O contato não pode ter mais que 20 caracteres.',
        ];
    }
}
