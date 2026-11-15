<?php

namespace App\Http\Controllers;

use App\Enums\StatusTriagem;
use App\Http\Requests\AdicionarPacienteFilaRequest;
use App\Http\Requests\StorePacienteRequest;
use App\Models\Paciente;
use App\Models\Triagem;
use App\Services\PacienteService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function __construct(private readonly PacienteService $pacienteService)
    {
    }

    public function index(Request $request)
    {
        $search = $request->input('busca');
        $searchPaciente = $request->input('busca_paciente');

        $fila = $this->pacienteService->fila($search);
        $resultadosPacientes = $this->pacienteService->buscar($searchPaciente);

        $pacientesNaFila = Triagem::where('status', StatusTriagem::Aguardando->value)->count();
        $totalCadastradosHoje = Paciente::whereDate('created_at', Carbon::today())->count();

        return view('recepcionista', compact(
            'fila',
            'pacientesNaFila',
            'totalCadastradosHoje',
            'search',
            'searchPaciente',
            'resultadosPacientes',
        ));
    }

    public function adicionarNaFila(AdicionarPacienteFilaRequest $request)
    {
        $adicionado = $this->pacienteService->adicionarNaFila((int) $request->validated('paciente_id'));

        if (! $adicionado) {
            return redirect()->back()->with('error', 'Este paciente já está aguardando na fila de triagem!');
        }

        return redirect()->route('recepcionista')->with('success', 'Paciente adicionado à fila com sucesso!');
    }

    public function store(StorePacienteRequest $request)
    {
        $this->pacienteService->cadastrar($request->validated());

        return redirect()->back()->with('success', 'Paciente cadastrado com sucesso na fila!');
    }

    public function destroy(Triagem $triagem)
    {
        $this->pacienteService->removerDaFila($triagem);

        return redirect()->back()->with('success', 'Paciente removido da fila com sucesso!');
    }
}
