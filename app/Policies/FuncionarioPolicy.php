<?php

namespace App\Policies;

use App\Enums\Perfil;
use App\Models\Funcionario;

class FuncionarioPolicy
{
    public function viewAny(Funcionario $usuario): bool
    {
        return $this->isAdministrador($usuario);
    }

    public function create(Funcionario $usuario): bool
    {
        return $this->isAdministrador($usuario);
    }

    public function delete(Funcionario $usuario, Funcionario $funcionario): bool
    {
        return $this->isAdministrador($usuario);
    }

    public function update(Funcionario $usuario, Funcionario $funcionario): bool
    {
        return $this->isProprioOuAdministrador($usuario, $funcionario);
    }

    public function edit(Funcionario $usuario, Funcionario $funcionario): bool
    {
        return $this->isProprioOuAdministrador($usuario, $funcionario);
    }

    private function isAdministrador(Funcionario $usuario): bool
    {
        return Perfil::tryFrom((string) $usuario->perfil) === Perfil::Administrador;
    }

    private function isProprioOuAdministrador(Funcionario $usuario, Funcionario $funcionario): bool
    {
        return $usuario->is($funcionario) || $this->isAdministrador($usuario);
    }
}
