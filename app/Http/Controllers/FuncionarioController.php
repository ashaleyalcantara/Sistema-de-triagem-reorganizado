<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFuncionarioRequest;
use App\Http\Requests\UpdateFuncionarioRequest;
use App\Models\Funcionario;
use App\Services\FuncionarioService;

class FuncionarioController extends Controller
{
    public function __construct(private readonly FuncionarioService $funcionarioService)
    {
    }

    public function perfil()
    {
        $funcionario = auth()->user();

        return view('funcionarios.perfil', compact('funcionario'));
    }

    public function index()
    {
        $this->authorize('viewAny', Funcionario::class);

        $funcionarios = $this->funcionarioService->listar();

        return view('Administrador.gestaoFuncionarios', compact('funcionarios'));
    }

    public function store(StoreFuncionarioRequest $request)
    {
        $this->authorize('create', Funcionario::class);

        $this->funcionarioService->criar($request->validated());

        return redirect('/gestaoFuncionarios')->with('sucesso', 'Funcionário cadastrado com sucesso!');
    }

    public function edit(Funcionario $funcionario)
    {
        $this->authorize('edit', $funcionario);

        return view('funcionarios.editar', compact('funcionario'));
    }

    public function update(UpdateFuncionarioRequest $request, Funcionario $funcionario)
    {
        $this->authorize('update', $funcionario);

        $this->funcionarioService->atualizar($funcionario, $request->validated());

        return redirect()->route('funcionarios.perfil')->with('success', 'Seu perfil foi atualizado com sucesso!');
    }

    public function destroy(Funcionario $funcionario)
    {
        $this->authorize('delete', $funcionario);

        $this->funcionarioService->excluir($funcionario);

        return redirect('/gestaoFuncionarios')->with('sucesso', 'Funcionário excluído com sucesso!');
    }
}
