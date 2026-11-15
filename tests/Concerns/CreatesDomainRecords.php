<?php

namespace Tests\Concerns;

use App\Enums\Perfil;
use App\Enums\StatusTriagem;
use App\Models\Funcionario;
use App\Models\Paciente;
use App\Models\Triagem;
use Illuminate\Support\Facades\Hash;

trait CreatesDomainRecords
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function criarFuncionario(string $perfil = Perfil::Enfermeiro->value, array $attributes = []): Funcionario
    {
        static $sequencia = 0;
        $sequencia++;

        return Funcionario::create(array_merge([
            'nome' => 'Funcionario '.$perfil.' '.$sequencia,
            'cpf' => str_pad((string) $sequencia, 11, '0', STR_PAD_LEFT),
            'email' => 'funcionario'.$sequencia.'@tria.com',
            'senha' => Hash::make('senha123'),
            'perfil' => $perfil,
        ], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function criarPaciente(array $attributes = []): Paciente
    {
        static $sequencia = 0;
        $sequencia++;

        return Paciente::create(array_merge([
            'nome_completo' => 'Paciente '.$sequencia,
            'cpf' => str_pad((string) (90000000000 + $sequencia), 11, '0', STR_PAD_LEFT),
            'data_nascimento' => '1990-01-01',
            'contato' => '11999999999',
        ], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function criarTriagem(?Paciente $paciente = null, array $attributes = []): Triagem
    {
        $paciente ??= $this->criarPaciente();

        return Triagem::create(array_merge([
            'paciente_id' => $paciente->id,
            'status' => StatusTriagem::Aguardando->value,
        ], $attributes));
    }
}
