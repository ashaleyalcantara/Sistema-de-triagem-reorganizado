<?php

namespace App\Services;

use App\Enums\StatusTriagem;
use App\Models\Paciente;
use App\Models\Triagem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PacienteService
{
    /**
     * @return Collection<int, Triagem>
     */
    public function fila(?string $busca = null): Collection
    {
        $query = Triagem::with('paciente')
            ->where('status', StatusTriagem::Aguardando->value);

        if ($busca) {
            $query->whereHas('paciente', function ($q) use ($busca) {
                $q->where('nome_completo', 'like', "%{$busca}%")
                    ->orWhere('cpf', 'like', "%{$busca}%");
            });
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    /**
     * @return Collection<int, Paciente>
     */
    public function buscar(?string $termo = null): Collection
    {
        if (! $termo) {
            return collect();
        }

        return Paciente::where('nome_completo', 'like', "%{$termo}%")
            ->orWhere('cpf', 'like', "%{$termo}%")
            ->limit(5)
            ->get();
    }

    public function adicionarNaFila(int $pacienteId): bool
    {
        $jaNaFila = Triagem::where('paciente_id', $pacienteId)
            ->where('status', StatusTriagem::Aguardando->value)
            ->exists();

        if ($jaNaFila) {
            return false;
        }

        Triagem::create([
            'paciente_id' => $pacienteId,
            'status' => StatusTriagem::Aguardando->value,
        ]);

        return true;
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function cadastrar(array $dados): Paciente
    {
        return DB::transaction(function () use ($dados) {
            $paciente = Paciente::create($dados);

            Triagem::create([
                'paciente_id' => $paciente->id,
                'status' => StatusTriagem::Aguardando->value,
            ]);

            return $paciente;
        });
    }

    public function removerDaFila(Triagem $triagem): void
    {
        $triagem->update([
            'status' => StatusTriagem::Removido->value,
            'updated_at' => now(),
        ]);
    }
}
