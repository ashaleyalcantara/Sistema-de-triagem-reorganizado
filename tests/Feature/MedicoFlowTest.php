<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use App\Enums\StatusTriagem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainRecords;
use Tests\TestCase;

class MedicoFlowTest extends TestCase
{
    use CreatesDomainRecords, RefreshDatabase;

    public function test_fila_do_medico_prioriza_pela_classificacao(): void
    {
        $medico = $this->criarFuncionario(Perfil::Medico->value);

        $poucoUrgente = $this->criarPaciente(['nome_completo' => 'Paciente Pouco Urgente']);
        $emergencia = $this->criarPaciente(['nome_completo' => 'Paciente Emergencia']);
        $urgente = $this->criarPaciente(['nome_completo' => 'Paciente Urgente']);

        $this->criarTriagem($poucoUrgente, [
            'status' => StatusTriagem::AguardandoMedico->value,
            'classificacao' => 'pouco_urgente',
        ]);
        $this->criarTriagem($emergencia, [
            'status' => StatusTriagem::AguardandoMedico->value,
            'classificacao' => 'emergencia',
        ]);
        $this->criarTriagem($urgente, [
            'status' => StatusTriagem::AguardandoMedico->value,
            'classificacao' => 'urgente',
        ]);

        $this->actingAs($medico)
            ->get('/medico')
            ->assertOk()
            ->assertSeeInOrder(['Paciente Emergencia', 'Paciente Urgente', 'Paciente Pouco Urgente']);
    }

    public function test_chamar_paciente_coloca_em_consulta_e_conclui_a_anterior(): void
    {
        $medico = $this->criarFuncionario(Perfil::Medico->value);

        $anterior = $this->criarTriagem(null, [
            'status' => StatusTriagem::EmConsulta->value,
            'classificacao' => 'urgente',
        ]);
        $proximo = $this->criarTriagem(null, [
            'status' => StatusTriagem::AguardandoMedico->value,
            'classificacao' => 'emergencia',
        ]);

        $this->actingAs($medico)
            ->post(route('medico.chamar', $proximo))
            ->assertRedirect();

        $this->assertSame(StatusTriagem::EmConsulta->value, $proximo->fresh()->status);
        $this->assertSame(StatusTriagem::Concluido->value, $anterior->fresh()->status);
    }

    public function test_finalizar_consulta_marca_como_concluida(): void
    {
        $medico = $this->criarFuncionario(Perfil::Medico->value);
        $triagem = $this->criarTriagem(null, [
            'status' => StatusTriagem::EmConsulta->value,
            'classificacao' => 'urgente',
        ]);

        $this->actingAs($medico)
            ->post(route('medico.finalizar', $triagem), ['descricao' => 'Paciente liberado'])
            ->assertRedirect(route('medico.index'));

        $this->assertSame(StatusTriagem::Concluido->value, $triagem->fresh()->status);
    }

    public function test_dashboard_mostra_paciente_em_consulta(): void
    {
        $medico = $this->criarFuncionario(Perfil::Medico->value);
        $triagem = $this->criarTriagem(null, [
            'status' => StatusTriagem::EmConsulta->value,
            'classificacao' => 'muito_urgente',
        ]);

        $this->actingAs($medico)
            ->get('/medico')
            ->assertOk()
            ->assertSee($triagem->paciente->nome_completo);
    }
}
