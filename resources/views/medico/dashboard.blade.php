@extends('layouts.app')

@section('title', 'Tria - Consulta Médica')

@section('content')
    <h1 class="text-2xl font-bold text-slate-800 -mb-2">Consulta médica</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card label="Aguardando Consulta" :value="$pacientesNaFila" compact />
        <x-stat-card label="Atendidos Hoje" :value="$atendidosHoje" compact />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <section class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4 lg:col-span-1">
            <h2 class="text-base font-bold text-slate-800">Pacientes para consultar</h2>

            <div class="space-y-3">
                @forelse($fila as $key => $item)
                    @php
                        $corBorda = match ($item->classificacao) {
                            'emergencia' => 'border-l-4 border-l-red-600',
                            'muito_urgente' => 'border-l-4 border-l-orange-500',
                            'urgente' => 'border-l-4 border-l-amber-400',
                            'pouco_urgente' => 'border-l-4 border-l-green-500',
                            default => 'border-l-4 border-l-blue-500',
                        };
                        $is_atual = $key === 0;
                    @endphp

                    <div
                        class="flex items-center justify-between p-3 rounded-xl border {{ $corBorda }} {{ $is_atual ? 'bg-cyan-50/80 border-cyan-300 ring-1 ring-cyan-300' : 'border-slate-100 bg-white' }}">
                        <div class="flex items-center space-x-3">
                            <span
                                class="bg-cyan-100 text-cyan-700 font-bold w-6 h-6 flex items-center justify-center rounded text-xs">
                                {{ $key + 1 }}
                            </span>
                            <div>
                                <h4 class="text-xs font-semibold text-slate-700">
                                    {{ $item->paciente->nome_completo }}
                                    @if ($is_atual && $item->status === 'em_consulta')
                                        <span
                                            class="text-[10px] text-cyan-600 font-bold ml-1 border border-cyan-400 px-1 rounded-md bg-white">
                                            Em Atendimento
                                        </span>
                                    @endif
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    ID: P-{{ str_pad($item->paciente->id, 3, '0', STR_PAD_LEFT) }} &bull; desde
                                    {{ $item->updated_at->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 text-slate-400 text-xs">
                            @if ($is_atual)
                                <form action="{{ route('medico.chamar', $item) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-cyan-500 hover:text-cyan-600 transition p-1"
                                        title="Chamar paciente no Painel">
                                        <i class="fa-solid fa-volume-high text-sm"></i>
                                    </button>
                                </form>
                            @else
                                <i class="fa-solid fa-volume-high opacity-30"></i>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-4">Nenhum paciente na fila.</p>
                @endforelse
            </div>
        </section>

        <section
            class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 min-h-[400px] lg:col-span-2 flex flex-col justify-center">
            @if ($pacienteSelecionado)
                @php
                    switch ($pacienteSelecionado->classificacao) {
                        case 'emergencia':
                            $classeCor = 'bg-red-600 text-white';
                            $textoUrgencia = 'Emergência - Imediato';
                            break;
                        case 'muito_urgente':
                            $classeCor = 'bg-orange-500 text-white';
                            $textoUrgencia = 'Muito Urgente - Até 10 min';
                            break;
                        case 'urgente':
                            $classeCor = 'bg-amber-400 text-slate-900';
                            $textoUrgencia = 'Urgente - Até 60 min';
                            break;
                        case 'pouco_urgente':
                            $classeCor = 'bg-green-500 text-white';
                            $textoUrgencia = 'Pouco Urgente - Até 120 min';
                            break;
                        default:
                            $classeCor = 'bg-blue-500 text-white';
                            $textoUrgencia = 'Não Urgente - Até 240 min';
                    }
                @endphp

                <form action="{{ route('medico.finalizar', $pacienteSelecionado) }}" method="POST"
                    class="space-y-5 h-full flex flex-col justify-between flex-grow">
                    @csrf
                    <div class="space-y-4">
                        <div class="border-b border-slate-100 pb-4 flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">
                                    {{ $pacienteSelecionado->paciente->nome_completo }}
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">
                                    CPF: {{ $pacienteSelecionado->paciente->cpf ?? 'Não informado' }} &bull;
                                    Idade:
                                    {{ \Carbon\Carbon::parse($pacienteSelecionado->paciente->data_nascimento)->age }}
                                    anos
                                </p>
                            </div>
                            <span
                                class="{{ $classeCor }} text-[9px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full flex items-center space-x-1">
                                <i class="fa-solid fa-circle text-[6px]"></i> <span>{{ $textoUrgencia }}</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    Sintomas Relatados
                                </p>
                                <p class="text-xs text-slate-700 mt-1 font-medium">
                                    {{ $pacienteSelecionado->sintomas ?? 'Não informados' }}
                                </p>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    Sinais Vitais
                                </p>
                                <p class="text-xs text-slate-700 mt-1 font-medium">
                                    PA: {{ $pacienteSelecionado->pressao }} &bull;
                                    FC: {{ $pacienteSelecionado->frequencia_cardiaca }} bpm &bull;
                                    Temp: {{ $pacienteSelecionado->temperatura }}°C
                                </p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label for="descricao"
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                                Descrição da Consulta
                            </label>
                            <textarea id="descricao" name="descricao" rows="6" required
                                placeholder="Digite as observações médicas, receitas ou orientações aqui..."
                                class="w-full p-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-cyan-500 transition resize-none"></textarea>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-cyan-500 text-white font-semibold py-2.5 rounded-xl hover:bg-cyan-600 transition shadow-sm mt-4">
                        Finalizar consulta
                    </button>
                </form>
            @else
                <div class="text-center py-12 space-y-3 max-w-xs mx-auto">
                    <div class="text-cyan-500 text-5xl opacity-80">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700">Nenhum paciente aguardando atendimento.</h3>
                </div>
            @endif
        </section>
    </div>
@endsection
