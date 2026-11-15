<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTriagemRequest;
use App\Models\Triagem;
use App\Services\TriagemService;
use Illuminate\Http\Request;

class TriagemController extends Controller
{
    public function __construct(private readonly TriagemService $triagemService)
    {
    }

    public function index(Request $request)
    {
        $fila = $this->triagemService->filaAtendimento();
        $indicadores = $this->triagemService->indicadores();

        $triagemSelecionada = $this->triagemService->pacienteSelecionado($fila);
        $pacienteSelecionado = $triagemSelecionada?->paciente;

        return view('triagem.painel', [
            'fila' => $fila,
            'pacientesNaFila' => $indicadores['pacientesNaFila'],
            'triadosHoje' => $indicadores['triadosHoje'],
            'pacienteSelecionado' => $pacienteSelecionado,
        ]);
    }

    public function chamarPaciente(Triagem $triagem)
    {
        $this->triagemService->chamar($triagem);

        return redirect()->route('triagem.index')->with('success', 'Paciente chamado no painel!');
    }

    public function store(StoreTriagemRequest $request)
    {
        $this->triagemService->salvarTriagem($request->validated());

        return redirect()->route('triagem.index')
            ->with('success', 'Triagem concluída! Paciente enviado para a fila do médico.');
    }

    public function painelTV()
    {
        $chamada = $this->triagemService->chamadaAtualPainel();

        return view('painelChamada', ['chamada' => $chamada]);
    }
}
