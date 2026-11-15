<?php

namespace App\Http\Requests;

use App\Models\Funcionario;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFuncionarioRequest extends FormRequest
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
        $funcionario = $this->route('funcionario');
        $funcionarioId = $funcionario instanceof Funcionario ? $funcionario->id : $funcionario;

        return [
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:11', 'unique:funcionarios,cpf,'.$funcionarioId],
            'email' => ['required', 'email', 'max:255', 'unique:funcionarios,email,'.$funcionarioId],
            'telefone' => ['nullable', 'string', 'max:20'],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remover_foto' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve conter exatamente 11 números.',
            'cpf.unique' => 'Este CPF já está cadastrado no sistema.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Insira um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado! Tente outro.',
            'telefone.max' => 'O telefone não pode ter mais que 20 caracteres.',
            'foto_perfil.image' => 'O arquivo enviado deve ser uma imagem.',
            'foto_perfil.mimes' => 'A foto deve ser do tipo jpeg, png, jpg ou webp.',
            'foto_perfil.max' => 'A foto não pode ter mais que 2 MB.',
        ];
    }
}
