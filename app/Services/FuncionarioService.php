<?php

namespace App\Services;

use App\Models\Funcionario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FuncionarioService
{
    /**
     * @return Collection<int, Funcionario>
     */
    public function listar(): Collection
    {
        return Funcionario::all();
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function criar(array $dados): Funcionario
    {
        return Funcionario::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'cpf' => $dados['cpf'],
            'senha' => Hash::make($dados['senha']),
            'perfil' => $dados['perfil'],
            'contato' => $dados['contato'] ?? null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $dados
     */
    public function atualizar(Funcionario $funcionario, array $dados): Funcionario
    {
        $foto = $dados['foto_perfil'] ?? null;

        if ($foto instanceof UploadedFile) {
            if ($funcionario->foto_perfil) {
                Storage::disk('public')->delete($funcionario->foto_perfil);
            }

            $funcionario->foto_perfil = $foto->store('funcionarios', 'public');
        } elseif (! empty($dados['remover_foto']) && $funcionario->foto_perfil) {
            Storage::disk('public')->delete($funcionario->foto_perfil);
            $funcionario->foto_perfil = null;
        }

        $funcionario->nome = $dados['nome'];
        $funcionario->cpf = $dados['cpf'];
        $funcionario->email = $dados['email'];
        $funcionario->contato = $dados['telefone'] ?? null;

        $funcionario->save();

        return $funcionario;
    }

    public function excluir(Funcionario $funcionario): void
    {
        $funcionario->delete();
    }
}
