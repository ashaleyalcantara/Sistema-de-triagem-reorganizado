<?php

namespace App\Http\Controllers;

use App\Models\Triagem;
use App\Services\MedicoService;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function __construct(private readonly MedicoService $medicoService)
    {
    }

    public function index(Request $request)
    {
        $fila = $this->medicoService->fila();

        return view('medico.dashboard', [
            'fila' => $fila,
            'pacientesNaFila' => $fila->count(),
            'atendidosHoje' => $this->medicoService->atendidosHoje(),
            'pacienteSelecionado' => $this->medicoService->pacienteSelecionado($fila),
        ]);
    }

    public function chamarPaciente(Triagem $triagem)
    {
        $this->medicoService->chamar($triagem);

        return redirect()->back()->with('success', 'Paciente chamado no painel com sucesso!');
    }

    public function finalizarConsulta(Request $request, Triagem $triagem)
    {
        $this->medicoService->finalizar($triagem);

        return redirect()->route('medico.index')->with('success', 'Consulta finalizada com sucesso!');
    }
}
