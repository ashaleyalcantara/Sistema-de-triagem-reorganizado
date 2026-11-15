<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use App\Enums\StatusTriagem;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainRecords;
use Tests\TestCase;

class PacienteFlowTest extends TestCase
{
    use CreatesDomainRecords, RefreshDatabase;

    public function test_recepcionista_cadastra_paciente_na_fila(): void
    {
        $recepcionista = $this->criarFuncionario(Perfil::Recepcionista->value);

        $response = $this->actingAs($recepcionista)->post(route('pacientes.store'), [
            'nome_completo' => 'Maria da Silva',
            'cpf' => '12345678901',
            'data_nascimento' => '1985-05-20',
            'contato' => '11955554444',
        ]);

        $response->assertRedirect();

        $paciente = Paciente::where('cpf', '12345678901')->firstOrFail();
        $this->assertSame('Maria da Silva', $paciente->nome_completo);

        $this->assertDatabaseHas('triagens', [
            'paciente_id' => $paciente->id,
            'status' => StatusTriagem::Aguardando->value,
        ]);
    }

    public function test_cadastro_de_paciente_valida_campos(): void
    {
        $recepcionista = $this->criarFuncionario(Perfil::Recepcionista->value);

        $this->actingAs($recepcionista)
            ->from('/recepcionista')
            ->post(route('pacientes.store'), [])
            ->assertRedirect('/recepcionista')
            ->assertSessionHasErrors(['nome_completo', 'data_nascimento']);
    }

    public function test_adiciona_paciente_existente_na_fila(): void
    {
        $recepcionista = $this->criarFuncionario(Perfil::Recepcionista->value);
        $paciente = $this->criarPaciente();

        $this->actingAs($recepcionista)
            ->post(route('triagem.adicionar'), ['paciente_id' => $paciente->id])
            ->assertRedirect(route('recepcionista'));

        $this->assertDatabaseHas('triagens', [
            'paciente_id' => $paciente->id,
            'status' => StatusTriagem::Aguardando->value,
        ]);
    }

    public function test_nao_duplica_paciente_na_fila(): void
    {
        $recepcionista = $this->criarFuncionario(Perfil::Recepcionista->value);
        $paciente = $this->criarPaciente();
        $this->criarTriagem($paciente);

        $this->actingAs($recepcionista)
            ->post(route('triagem.adicionar'), ['paciente_id' => $paciente->id])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseCount('triagens', 1);
    }

    public function test_recepcionista_remove_paciente_da_fila(): void
    {
        $recepcionista = $this->criarFuncionario(Perfil::Recepcionista->value);
        $triagem = $this->criarTriagem();

        $this->actingAs($recepcionista)
            ->delete(route('triagem.destroy', $triagem))
            ->assertRedirect();

        $this->assertSame(StatusTriagem::Removido->value, $triagem->fresh()->status);
    }

    public function test_busca_de_paciente_na_recepcao(): void
    {
        $recepcionista = $this->criarFuncionario(Perfil::Recepcionista->value);
        $paciente = $this->criarPaciente(['nome_completo' => 'Joana Pesquisavel']);

        $this->actingAs($recepcionista)
            ->get('/recepcionista?busca_paciente=Joana')
            ->assertOk()
            ->assertSee($paciente->nome_completo);
    }
}
