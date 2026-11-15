<?php

namespace Tests\Feature;

use App\Enums\Perfil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesDomainRecords;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use CreatesDomainRecords, RefreshDatabase;

    public function test_pagina_de_login_e_exibida(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Acessar o sistema');
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function perfisValidos(): array
    {
        return [
            'administrador' => [Perfil::Administrador->value, 'administrador', 'admin'],
            'recepcionista' => [Perfil::Recepcionista->value, 'recepcionista', 'recepcionista'],
            'enfermeiro' => [Perfil::Enfermeiro->value, 'enfermeiro', 'triagem.index'],
            'medico' => [Perfil::Medico->value, 'medico', 'medico.index'],
        ];
    }

    #[DataProvider('perfisValidos')]
    public function test_login_autentica_e_redireciona_conforme_perfil(
        string $perfilBanco,
        string $perfilFormulario,
        string $rotaDestino,
    ): void {
        $funcionario = $this->criarFuncionario($perfilBanco);

        $response = $this->post('/login', [
            'email' => $funcionario->email,
            'senha' => 'senha123',
            'perfil' => $perfilFormulario,
        ]);

        $response->assertRedirect(route($rotaDestino));
        $this->assertAuthenticatedAs($funcionario);
    }

    public function test_login_com_senha_invalida_nao_autentica(): void
    {
        $funcionario = $this->criarFuncionario();

        $response = $this->from('/login')->post('/login', [
            'email' => $funcionario->email,
            'senha' => 'senha-errada',
            'perfil' => 'enfermeiro',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login_error');
        $this->assertGuest();
    }

    public function test_login_com_perfil_diferente_do_cadastro_nao_autentica(): void
    {
        $funcionario = $this->criarFuncionario(Perfil::Enfermeiro->value);

        $response = $this->from('/login')->post('/login', [
            'email' => $funcionario->email,
            'senha' => 'senha123',
            'perfil' => 'medico',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login_error');
        $this->assertGuest();
    }

    public function test_logout_encerra_a_sessao(): void
    {
        $funcionario = $this->criarFuncionario();

        $response = $this->actingAs($funcionario)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
