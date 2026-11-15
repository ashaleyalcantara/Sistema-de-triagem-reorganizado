<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTriagemRequest extends FormRequest
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
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'sintomas' => ['required', 'string'],
            'pressao' => ['required', 'string'],
            'frequencia_cardiaca' => ['required', 'string'],
            'temperatura' => ['required', 'string'],
            'classificacao' => ['required', 'in:emergencia,muito_urgente,urgente,pouco_urgente,nao_urgente'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'paciente_id.required' => 'Selecione um paciente para a triagem.',
            'paciente_id.exists' => 'O paciente informado não existe.',
            'sintomas.required' => 'Descreva os sintomas do paciente.',
            'pressao.required' => 'Informe a pressão arterial.',
            'frequencia_cardiaca.required' => 'Informe a frequência cardíaca.',
            'temperatura.required' => 'Informe a temperatura.',
            'classificacao.required' => 'Selecione a classificação de risco.',
            'classificacao.in' => 'Classificação de risco inválida.',
        ];
    }
}
