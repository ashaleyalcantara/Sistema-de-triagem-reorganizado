<?php

namespace App\Enums;

enum Perfil: string
{
    case Administrador = 'Administrador';
    case Recepcionista = 'Recepcionista';
    case Enfermeiro = 'Enfermeiro';
    case Medico = 'Médico';

    public function label(): string
    {
        return $this->value;
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
