<?php

namespace App\Enums;

enum StatusTriagem: string
{
    case Aguardando = 'aguardando';
    case EmTriagem = 'em_triagem';
    case AguardandoMedico = 'aguardando_medico';
    case EmConsulta = 'em_consulta';
    case Concluido = 'concluido';
    case Removido = 'removido';

    public function label(): string
    {
        return match ($this) {
            self::Aguardando => 'Aguardando',
            self::EmTriagem => 'Em triagem',
            self::AguardandoMedico => 'Aguardando médico',
            self::EmConsulta => 'Em consulta',
            self::Concluido => 'Concluído',
            self::Removido => 'Removido',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
