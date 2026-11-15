<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
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
            'nome_completo' => ['required', 'string', 'max:255'],
            'cpf' => ['nullable', 'string', 'digits:11', 'unique:pacientes,cpf'],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'contato' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome_completo.required' => 'O campo nome completo é obrigatório.',
            'nome_completo.max' => 'O nome não pode ter mais que 255 caracteres.',

            'cpf.digits' => 'O CPF deve conter exatamente 11 números.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',

            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.date' => 'Insira uma data válida.',
            'data_nascimento.before' => 'A data de nascimento deve ser anterior ao dia de hoje.',

            'contato.max' => 'O contato não pode ter mais que 20 caracteres.',
        ];
    }
}
