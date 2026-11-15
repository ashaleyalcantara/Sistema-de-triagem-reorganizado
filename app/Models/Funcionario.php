<?php

namespace App\Models;

use App\Enums\Perfil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Funcionario extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'funcionarios';

    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'contato',
        'senha',
        'perfil',
        'foto_perfil',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // O Laravel precisa saber onde está a senha criptografada.
    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function isAdministrador(): bool
    {
        return $this->perfil === Perfil::Administrador->value;
    }

    public function isRecepcionista(): bool
    {
        return $this->perfil === Perfil::Recepcionista->value;
    }

    public function isEnfermeiro(): bool
    {
        return $this->perfil === Perfil::Enfermeiro->value;
    }

    public function isMedico(): bool
    {
        return $this->perfil === Perfil::Medico->value;
    }

    public function redirectRouteName(): ?string
    {
        return match (Perfil::tryFrom((string) $this->perfil)) {
            Perfil::Administrador => 'admin',
            Perfil::Recepcionista => 'recepcionista',
            Perfil::Enfermeiro => 'triagem.index',
            Perfil::Medico => 'medico.index',
            default => null,
        };
    }
}
