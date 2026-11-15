<?php

namespace App\Enums;

enum ClassificacaoRisco: string
{
    case Emergencia = 'emergencia';
    case MuitoUrgente = 'muito_urgente';
    case Urgente = 'urgente';
    case PoucoUrgente = 'pouco_urgente';
    case NaoUrgente = 'nao_urgente';

    public function label(): string
    {
        return match ($this) {
            self::Emergencia => 'Emergência',
            self::MuitoUrgente => 'Muito Urgente',
            self::Urgente => 'Urgente',
            self::PoucoUrgente => 'Pouco Urgente',
            self::NaoUrgente => 'Não Urgente',
        };
    }

    public function prioridade(): int
    {
        return match ($this) {
            self::Emergencia => 1,
            self::MuitoUrgente => 2,
            self::Urgente => 3,
            self::PoucoUrgente => 4,
            self::NaoUrgente => 5,
        };
    }

    public function tempoAlvo(): string
    {
        return match ($this) {
            self::Emergencia => 'Imediato',
            self::MuitoUrgente => 'Até 10 min',
            self::Urgente => 'Até 60 min',
            self::PoucoUrgente => 'Até 120 min',
            self::NaoUrgente => 'Até 240 min',
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
