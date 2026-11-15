<?php

namespace App\Services;

use App\Enums\StatusTriagem;
use App\Models\Triagem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MedicoService
{
    /**
     * Fila do médico ordenada por prioridade (Protocolo de Manchester).
     *
     * @return Collection<int, Triagem>
     */
    public function fila(): Collection
    {
        return Triagem::with('paciente')
            ->where('status', StatusTriagem::AguardandoMedico->value)
            ->orderByRaw("CASE classificacao
                WHEN 'emergencia' THEN 1
                WHEN 'muito_urgente' THEN 2
                WHEN 'urgente' THEN 3
                WHEN 'pouco_urgente' THEN 4
                WHEN 'nao_urgente' THEN 5
                ELSE 6 END ASC")
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function atendidosHoje(): int
    {
        return Triagem::where('status', StatusTriagem::Concluido->value)
            ->whereDate('updated_at', today())
            ->count();
    }

    /**
     * @param  Collection<int, Triagem>  $fila
     */
    public function pacienteSelecionado(Collection $fila): ?Triagem
    {
        $emConsulta = Triagem::with('paciente')
            ->where('status', StatusTriagem::EmConsulta->value)
            ->latest('updated_at')
            ->first();

        return $emConsulta ?? $fila->first();
    }

    public function chamar(Triagem $triagem): Triagem
    {
        return DB::transaction(function () use ($triagem) {
            Triagem::where('status', StatusTriagem::EmConsulta->value)
                ->update(['status' => StatusTriagem::Concluido->value]);

            $triagem->update([
                'status' => StatusTriagem::EmConsulta->value,
                'updated_at' => now(),
            ]);

            return $triagem;
        });
    }

    public function finalizar(Triagem $triagem): Triagem
    {
        $triagem->update([
            'status' => StatusTriagem::Concluido->value,
            'updated_at' => now(),
        ]);

        return $triagem;
    }
}
