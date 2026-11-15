<?php

namespace App\Services;

use App\Enums\StatusTriagem;
use App\Models\Triagem;
use Illuminate\Support\Collection;

class TriagemService
{
    /**
     * Pacientes aguardando o início da triagem.
     *
     * @return Collection<int, Triagem>
     */
    public function filaAguardando(): Collection
    {
        return Triagem::with('paciente')
            ->where('status', StatusTriagem::Aguardando->value)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Fila visível para o enfermeiro (aguardando ou em triagem).
     *
     * @return Collection<int, Triagem>
     */
    public function filaAtendimento(): Collection
    {
        return Triagem::with('paciente')
            ->whereIn('status', [
                StatusTriagem::Aguardando->value,
                StatusTriagem::EmTriagem->value,
            ])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * @return array{pacientesNaFila: int, triadosHoje: int}
     */
    public function indicadores(): array
    {
        return [
            'pacientesNaFila' => Triagem::where('status', StatusTriagem::Aguardando->value)->count(),
            'triadosHoje' => Triagem::where('status', StatusTriagem::AguardandoMedico->value)
                ->whereDate('updated_at', today())
                ->count(),
        ];
    }

    /**
     * @param  Collection<int, Triagem>  $fila
     */
    public function pacienteSelecionado(Collection $fila): ?Triagem
    {
        $primeiroDaFila = $fila->first();

        return $primeiroDaFila?->status === StatusTriagem::EmTriagem->value
            ? $primeiroDaFila
            : null;
    }

    public function chamar(Triagem $triagem): Triagem
    {
        abort_unless(
            $triagem->status === StatusTriagem::Aguardando->value,
            422,
            'Este paciente não está aguardando na fila de triagem.'
        );

        $triagem->update([
            'status' => StatusTriagem::EmTriagem->value,
            'updated_at' => now(),
        ]);

        return $triagem;
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function salvarTriagem(array $dados): ?Triagem
    {
        $triagem = Triagem::where('paciente_id', $dados['paciente_id'])
            ->where('status', StatusTriagem::EmTriagem->value)
            ->first();

        if (! $triagem) {
            return null;
        }

        $triagem->update([
            'sintomas' => $dados['sintomas'],
            'pressao' => $dados['pressao'],
            'frequencia_cardiaca' => $dados['frequencia_cardiaca'],
            'temperatura' => $dados['temperatura'],
            'classificacao' => $dados['classificacao'],
            'status' => StatusTriagem::AguardandoMedico->value,
            'updated_at' => now(),
        ]);

        return $triagem;
    }

    public function chamadaAtualPainel(): ?Triagem
    {
        $registro = Triagem::with('paciente')
            ->whereIn('status', [
                StatusTriagem::EmTriagem->value,
                StatusTriagem::EmConsulta->value,
            ])
            ->orderBy('updated_at', 'desc')
            ->first();

        if ($registro) {
            return $registro;
        }

        return Triagem::with('paciente')
            ->whereIn('status', [
                StatusTriagem::EmTriagem->value,
                StatusTriagem::AguardandoMedico->value,
                StatusTriagem::EmConsulta->value,
                StatusTriagem::Concluido->value,
            ])
            ->whereDate('updated_at', today())
            ->orderBy('updated_at', 'desc')
            ->first();
    }
}
