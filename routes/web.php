<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\TriagemController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROTAS PÚBLICAS
// ==========================================

Route::get('/', [TriagemController::class, 'painelTV'])->name('painel.chamada');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// ROTAS PROTEGIDAS
// ==========================================

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Perfil (qualquer usuário autenticado)
    Route::get('/perfil', [FuncionarioController::class, 'perfil'])->name('funcionarios.perfil');
    Route::get('/funcionarios/{funcionario}/editar', [FuncionarioController::class, 'edit'])->name('funcionarios.edit');
    Route::put('/funcionarios/{funcionario}', [FuncionarioController::class, 'update'])->name('funcionarios.update');

    // Gestão de Funcionários - Administrador
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/gestaoFuncionarios', [FuncionarioController::class, 'index'])->name('admin');
        Route::post('/gestaoFuncionarios', [FuncionarioController::class, 'store'])->name('funcionarios.store');
        Route::delete('/funcionarios/{funcionario}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');
    });

    // Recepção de Pacientes
    Route::middleware('role:Recepcionista')->group(function () {
        Route::get('/recepcionista', [PacienteController::class, 'index'])->name('recepcionista');
        Route::post('/pacientes', [PacienteController::class, 'store'])->name('pacientes.store');
        Route::post('/triagem/adicionar', [PacienteController::class, 'adicionarNaFila'])->name('triagem.adicionar');
        Route::delete('/triagem/{triagem}', [PacienteController::class, 'destroy'])->name('triagem.destroy');
    });

    // Triagem - Enfermeiro
    Route::middleware('role:Enfermeiro')->group(function () {
        Route::get('/triagem', [TriagemController::class, 'index'])->name('triagem.index');
        Route::post('/triagem', [TriagemController::class, 'store'])->name('triagem.store');
        Route::post('/triagem/chamar/{triagem}', [TriagemController::class, 'chamarPaciente'])->name('triagem.chamar');
    });

    // Consulta - Médico
    Route::middleware('role:Médico')->group(function () {
        Route::get('/medico', [MedicoController::class, 'index'])->name('medico.index');
        Route::post('/medico/finalizar/{triagem}', [MedicoController::class, 'finalizarConsulta'])->name('medico.finalizar');
        Route::post('/medico/chamar/{triagem}', [MedicoController::class, 'chamarPaciente'])->name('medico.chamar');
    });
});
