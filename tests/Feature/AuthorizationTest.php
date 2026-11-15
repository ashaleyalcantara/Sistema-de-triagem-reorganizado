<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesDomainRecords;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use CreatesDomainRecords, RefreshDatabase;

    public function test_visitante_e_redirecionado_para_o_login(): void
    {
        $this->get('/recepcionista')->assertRedirect(route('login'));
        $this->get('/triagem')->assertRedirect(route('login'));
        $this->get('/medico')->assertRedirect(route('login'));
        $this->get('/gestaoFuncionarios')->assertRedirect(route('login'));
    }

    public function test_recepcionista_acessa_sua_area_e_nao_as_demais(): void
    {
        $recepcionista = $this->criarFuncionario(Perfil::Recepcionista->value);

        $this->actingAs($recepcionista)->get('/recepcionista')->assertOk();
        $this->actingAs($recepcionista)->get('/triagem')->assertForbidden();
        $this->actingAs($recepcionista)->get('/medico')->assertForbidden();
        $this->actingAs($recepcionista)->get('/gestaoFuncionarios')->assertForbidden();
    }

    public function test_enfermeiro_acessa_sua_area_e_nao_as_demais(): void
    {
        $enfermeiro = $this->criarFuncionario(Perfil::Enfermeiro->value);

        $this->actingAs($enfermeiro)->get('/triagem')->assertOk();
        $this->actingAs($enfermeiro)->get('/recepcionista')->assertForbidden();
        $this->actingAs($enfermeiro)->get('/medico')->assertForbidden();
        $this->actingAs($enfermeiro)->get('/gestaoFuncionarios')->assertForbidden();
    }

    public function test_medico_acessa_sua_area_e_nao_as_demais(): void
    {
        $medico = $this->criarFuncionario(Perfil::Medico->value);

        $this->actingAs($medico)->get('/medico')->assertOk();
        $this->actingAs($medico)->get('/recepcionista')->assertForbidden();
        $this->actingAs($medico)->get('/triagem')->assertForbidden();
        $this->actingAs($medico)->get('/gestaoFuncionarios')->assertForbidden();
    }

    public function test_administrador_tem_acesso_irrestrito(): void
    {
        $administrador = $this->criarFuncionario(Perfil::Administrador->value);

        $this->actingAs($administrador)->get('/gestaoFuncionarios')->assertOk();
        $this->actingAs($administrador)->get('/recepcionista')->assertOk();
        $this->actingAs($administrador)->get('/triagem')->assertOk();
        $this->actingAs($administrador)->get('/medico')->assertOk();
    }

    public function test_todos_os_perfis_acessam_o_proprio_perfil(): void
    {
        foreach (Perfil::values() as $perfil) {
            $funcionario = $this->criarFuncionario($perfil);

            $this->actingAs($funcionario)->get('/perfil')->assertOk();
        }
    }
}
