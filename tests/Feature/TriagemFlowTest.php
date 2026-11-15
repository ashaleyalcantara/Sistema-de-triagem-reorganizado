<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use App\Enums\StatusTriagem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainRecords;
use Tests\TestCase;

class TriagemFlowTest extends TestCase
{
    use CreatesDomainRecords, RefreshDatabase;

    public function test_painel_de_triagem_lista_pacientes_da_fila(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);
        $triagem = $this->criarTriagem(null, [
            'status' => StatusTriagem::Aguardando->value,
        ]);

        $this->actingAs($enfermeiro)
            ->get('/triagem')
            ->assertOk()
            ->assertSee($triagem->paciente->nome_completo);
    }

    public function test_chamar_paciente_coloca_em_triagem(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);
        $triagem = $this->criarTriagem();

        $response = $this->actingAs($enfermeiro)->post(route('triagem.chamar', $triagem));

        $response->assertRedirect(route('triagem.index'));
        $this->assertSame(StatusTriagem::EmTriagem->value, $triagem->fresh()->status);
    }

    public function test_nao_e_possivel_chamar_paciente_que_nao_esta_aguardando(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);
        $triagem = $this->criarTriagem(null, [
            'status' => StatusTriagem::AguardandoMedico->value,
        ]);

        $this->actingAs($enfermeiro)
            ->post(route('triagem.chamar', $triagem))
            ->assertStatus(422);
    }

    public function test_concluir_triagem_salva_dados_e_envia_para_o_medico(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);
        $triagem = $this->criarTriagem(null, ['status' => StatusTriagem::EmTriagem->value]);

        $response = $this->actingAs($enfermeiro)->post(route('triagem.store'), [
            'paciente_id' => $triagem->paciente_id,
            'sintomas' => 'Dor de cabeça intensa',
            'pressao' => '120/80',
            'frequencia_cardiaca' => '88',
            'temperatura' => '36.8',
            'classificacao' => 'urgente',
        ]);

        $response->assertRedirect(route('triagem.index'));

        $triagem = $triagem->fresh();
        $this->assertSame(StatusTriagem::AguardandoMedico->value, $triagem->status);
        $this->assertSame('Dor de cabeça intensa', $triagem->sintomas);
        $this->assertSame('urgente', $triagem->classificacao);
    }

    public function test_concluir_triagem_valida_a_classificacao(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);
        $triagem = $this->criarTriagem(null, ['status' => StatusTriagem::EmTriagem->value]);

        $this->actingAs($enfermeiro)
            ->from('/triagem')
            ->post(route('triagem.store'), [
                'paciente_id' => $triagem->paciente_id,
                'sintomas' => 'Febre',
                'pressao' => '110/70',
                'frequencia_cardiaca' => '90',
                'temperatura' => '38.5',
                'classificacao' => 'inexistente',
            ])
            ->assertRedirect('/triagem')
            ->assertSessionHasErrors('classificacao');

        $this->assertSame(StatusTriagem::EmTriagem->value, $triagem->fresh()->status);
    }

    public function test_painel_de_chamada_publico_exibe_ultima_chamada(): void
    {
        $triagem = $this->criarTriagem(null, [
            'status' => StatusTriagem::EmTriagem->value,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee($triagem->paciente->nome_completo);
    }
}
