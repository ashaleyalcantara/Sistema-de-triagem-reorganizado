<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainRecords;
use Tests\TestCase;

class FuncionarioManagementTest extends TestCase
{
    use CreatesDomainRecords, RefreshDatabase;

    public function test_administrador_cadastra_funcionario(): void
    {
        $administrador = $this->criarFuncionario(Perfil::Administrador->value);

        $response = $this->actingAs($administrador)->post(route('funcionarios.store'), [
            'nome' => 'Novo Medico',
            'email' => 'novo.medico@tria.com',
            'cpf' => '12345678901',
            'senha' => 'senha123',
            'perfil' => Perfil::Medico->value,
            'contato' => '11988887777',
        ]);

        $response->assertRedirect('/gestaoFuncionarios');

        $this->assertDatabaseHas('funcionarios', [
            'email' => 'novo.medico@tria.com',
            'perfil' => Perfil::Medico->value,
        ]);
    }

    public function test_cadastro_de_funcionario_valida_campos_obrigatorios(): void
    {
        $administrador = $this->criarFuncionario(Perfil::Administrador->value);

        $this->actingAs($administrador)
            ->from('/gestaoFuncionarios')
            ->post(route('funcionarios.store'), [])
            ->assertRedirect('/gestaoFuncionarios')
            ->assertSessionHasErrors(['nome', 'email', 'cpf', 'senha', 'perfil']);
    }

    public function test_nao_administrador_nao_cadastra_funcionario(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);

        $this->actingAs($enfermeiro)
            ->post(route('funcionarios.store'), [
                'nome' => 'Intruso',
                'email' => 'intruso@tria.com',
                'cpf' => '99988877766',
                'senha' => 'senha123',
                'perfil' => Perfil::Enfermeiro->value,
            ])
            ->assertForbidden();
    }

    public function test_funcionario_edita_o_proprio_perfil(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);

        $this->actingAs($enfermeiro)
            ->get(route('funcionarios.edit', $enfermeiro))
            ->assertOk();

        $response = $this->actingAs($enfermeiro)->put(route('funcionarios.update', $enfermeiro), [
            'nome' => 'Nome Atualizado',
            'cpf' => $enfermeiro->cpf,
            'email' => $enfermeiro->email,
            'telefone' => '11977776666',
        ]);

        $response->assertRedirect(route('funcionarios.perfil'));

        $enfermeiro = $enfermeiro->fresh();
        $this->assertSame('Nome Atualizado', $enfermeiro->nome);
        $this->assertSame('11977776666', $enfermeiro->contato);
    }

    public function test_funcionario_nao_edita_outro_funcionario(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);
        $outro = $this->criarFuncionario(Perfil::Recepcionista->value);

        $this->actingAs($enfermeiro)
            ->get(route('funcionarios.edit', $outro))
            ->assertForbidden();

        $this->actingAs($enfermeiro)
            ->put(route('funcionarios.update', $outro), [
                'nome' => 'Invasao',
                'cpf' => $outro->cpf,
                'email' => $outro->email,
            ])
            ->assertForbidden();

        $this->assertNotSame('Invasao', $outro->fresh()->nome);
    }

    public function test_administrador_edita_qualquer_funcionario(): void
    {
        $administrador = $this->criarFuncionario(Perfil::Administrador->value);
        $medico = $this->criarFuncionario(Perfil::Medico->value);

        $this->actingAs($administrador)
            ->get(route('funcionarios.edit', $medico))
            ->assertOk();

        $this->actingAs($administrador)
            ->put(route('funcionarios.update', $medico), [
                'nome' => 'Medico Editado',
                'cpf' => $medico->cpf,
                'email' => $medico->email,
            ])
            ->assertRedirect(route('funcionarios.perfil'));

        $this->assertSame('Medico Editado', $medico->fresh()->nome);
    }

    public function test_administrador_exclui_funcionario_com_soft_delete(): void
    {
        $administrador = $this->criarFuncionario(Perfil::Administrador->value);
        $alvo = $this->criarFuncionario(Perfil::Recepcionista->value);

        $this->actingAs($administrador)
            ->delete(route('funcionarios.destroy', $alvo))
            ->assertRedirect('/gestaoFuncionarios');

        $this->assertSoftDeleted('funcionarios', ['id' => $alvo->id]);
    }

    public function test_nao_administrador_nao_exclui_funcionario(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);
        $alvo = $this->criarFuncionario(Perfil::Recepcionista->value);

        $this->actingAs($enfermeiro)
            ->delete(route('funcionarios.destroy', $alvo))
            ->assertForbidden();

        $this->assertDatabaseHas('funcionarios', ['id' => $alvo->id, 'deleted_at' => null]);
    }
}
