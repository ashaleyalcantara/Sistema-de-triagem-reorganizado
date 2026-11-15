@extends('layouts.app')

@section('title', 'Tria - Triagem')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-stat-card label="Na Fila de Triagem" :value="$pacientesNaFila ?? 0" />
        <x-stat-card label="Cadastrados Hoje" :value="$totalCadastradosHoje ?? 0" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <div class="space-y-6">
            <section class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-magnifying-glass-plus text-cyan-500"></i>
                    <span>Adicionar paciente já cadastrado</span>
                </h2>

                <form action="{{ route('recepcionista') }}" method="GET" class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-cyan-500">
                        <i class="fa-solid fa-search"></i>
                    </span>

                    <input type="text" name="busca_paciente" value="{{ $searchPaciente ?? '' }}"
                        placeholder="Buscar por nome ou CPF para pôr na fila..."
                        class="w-full pl-9 pr-20 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-cyan-500 transition">

                    @if (!empty($searchPaciente))
                        <a href="{{ route('recepcionista') }}"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-red-400 hover:text-red-600 font-medium">
                            Limpar
                        </a>
                    @endif
                </form>

                @if (!empty($resultadosPacientes))
                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-2 max-h-52 overflow-y-auto">
                        <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            Resultados encontrados
                        </p>

                        @forelse($resultadosPacientes as $paciente)
                            <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-slate-200 shadow-xs">
                                <div>
                                    <p class="text-sm font-semibold text-slate-700">{{ $paciente->nome_completo }}</p>
                                    <p class="text-slate-400 text-xs">CPF: {{ $paciente->cpf ?? 'Não informado' }}</p>
                                </div>

                                <form action="{{ route('triagem.adicionar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">

                                    <button type="submit"
                                        class="bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-bold px-4 py-1.5 rounded-full transition shadow-sm flex items-center space-x-1">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                        <span>Fila</span>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic text-center py-2">
                                Nenhum paciente encontrado com esse termo.
                            </p>
                        @endforelse
                    </div>
                @endif
            </section>

            <section class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-6">
                <h2 class="text-xl font-bold text-slate-800">Cadastrar paciente</h2>

                <form action="{{ route('pacientes.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="nome_completo" class="block text-xs font-semibold text-slate-600 mb-1">
                            Nome completo
                        </label>
                        <input type="text" id="nome_completo" name="nome_completo" value="{{ old('nome_completo') }}"
                            class="w-full px-3 py-2 border @error('nome_completo') border-red-400 focus:border-red-500 @else border-slate-200 focus:border-cyan-500 @enderror rounded-lg focus:outline-none transition">

                        @error('nome_completo')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="cpf" class="block text-xs font-semibold text-slate-600 mb-1">
                            CPF (apenas números)
                        </label>
                        <input type="text" id="cpf" name="cpf" maxlength="11" value="{{ old('cpf') }}"
                            class="w-full px-3 py-2 border @error('cpf') border-red-400 focus:border-red-500 @else border-slate-200 focus:border-cyan-500 @enderror rounded-lg focus:outline-none transition">

                        @error('cpf')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="data_nascimento" class="block text-xs font-semibold text-slate-600 mb-1">
                            Data de Nascimento
                        </label>
                        <input type="date" id="data_nascimento" name="data_nascimento" value="{{ old('data_nascimento') }}"
                            class="w-full px-3 py-2 border @error('data_nascimento') border-red-400 focus:border-red-500 @else border-slate-200 focus:border-cyan-500 @enderror rounded-lg focus:outline-none transition">

                        @error('data_nascimento')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="contato" class="block text-xs font-semibold text-slate-600 mb-1">
                            Telefone / Contato
                        </label>
                        <input type="text" id="contato" name="contato" value="{{ old('contato') }}"
                            class="w-full px-3 py-2 border @error('contato') border-red-400 focus:border-red-500 @else border-slate-200 focus:border-cyan-500 @enderror rounded-lg focus:outline-none transition">

                        @error('contato')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-cyan-500 text-white font-semibold py-2.5 rounded-full hover:bg-cyan-600 transition mt-2 shadow-sm">
                        Cadastrar paciente
                    </button>
                </form>
            </section>
        </div>

        <section class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Fila de Triagem</h2>
                <p class="text-xs text-cyan-600 font-medium">Pacientes aguardando avaliação clínica</p>
            </div>

            <div class="relative">
                <form action="{{ route('recepcionista') }}" method="GET" class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-cyan-500">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <input type="text" name="busca" value="{{ $search ?? '' }}"
                        placeholder="Busca por nome ou CPF..."
                        class="w-full pl-9 pr-20 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-cyan-500 transition">

                    @if (!empty($search))
                        <a href="{{ route('recepcionista') }}"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-red-400 hover:text-red-600 font-medium">
                            Limpar
                        </a>
                    @endif
                </form>
            </div>

            <div class="space-y-3 pt-2">
                @forelse($fila as $key => $item)
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 last:border-none last:pb-0">
                        <div class="flex items-center space-x-3">
                            <span
                                class="bg-cyan-100 text-cyan-700 font-bold w-6 h-6 flex items-center justify-center rounded text-sm">
                                {{ $key + 1 }}
                            </span>

                            <div>
                                <h4 class="text-sm font-semibold text-slate-700">
                                    {{ $item->paciente->nome_completo }}
                                </h4>
                                <p class="text-xs text-slate-400">
                                    ID: P-{{ str_pad($item->paciente->id, 3, '0', STR_PAD_LEFT) }} &bull;
                                    {{ \Carbon\Carbon::parse($item->paciente->data_nascimento)->age }} anos &bull;
                                    desde {{ $item->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('triagem.destroy', $item) }}" method="POST"
                            onsubmit="return confirm('Deseja mesmo remover este paciente da fila?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                <i class="fa-regular fa-circle-xmark text-lg"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-sm italic">
                        @if (!empty($search))
                            Nenhum paciente encontrado para "{{ $search }}".
                        @else
                            Nenhum paciente na fila no momento.
                        @endif
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
