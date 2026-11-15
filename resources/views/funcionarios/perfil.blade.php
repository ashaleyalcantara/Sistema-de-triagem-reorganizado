@extends('layouts.app')

@section('title', 'Tria - Perfil')
@section('main-class', 'max-w-6xl mx-auto p-6 md:p-10 space-y-6')

@section('content')
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Perfil</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
        <div
            class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 flex flex-col items-center justify-center text-center">
            <div
                class="relative w-32 h-32 flex items-center justify-center bg-sky-100 text-cyan-700 rounded-full font-bold text-3xl tracking-wider">
                @if ($funcionario->foto_perfil)
                    <img src="{{ asset('storage/' . $funcionario->foto_perfil) }}" alt="Foto de perfil"
                        class="w-full h-full object-cover rounded-full">
                @else
                    {{ mb_strtoupper(mb_substr($funcionario->nome, 0, 2)) }}
                @endif

                <div
                    class="absolute bottom-0 right-0 bg-cyan-500 text-white p-2 rounded-full shadow-md flex items-center justify-center pointer-events-none">
                    <i data-lucide="image" class="w-4 h-4"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 md:col-span-2 space-y-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Dados cadastrais</h2>

            <div class="space-y-5">
                <div>
                    <label for="nome" class="block text-sm font-semibold text-slate-700 mb-1.5">Nome completo</label>
                    <input type="text" id="nome" value="{{ $funcionario->nome ?? '' }}" disabled
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-sm outline-none cursor-not-allowed">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="cpf" class="block text-sm font-semibold text-slate-700 mb-1.5">CPF</label>
                        <input type="text" id="cpf" value="{{ $funcionario->cpf ?? '' }}" disabled
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-sm outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label for="telefone" class="block text-sm font-semibold text-slate-700 mb-1.5">Telefone</label>
                        <input type="text" id="telefone" value="{{ $funcionario->contato ?? 'Não informado' }}" disabled
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-sm outline-none cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        E-mail de contato
                    </label>
                    <input type="email" id="email" value="{{ $funcionario->email ?? '' }}" disabled
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-sm outline-none cursor-not-allowed">
                </div>

                <div>
                    <label for="perfil" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Cargo / Função
                    </label>
                    <input type="text" id="perfil" value="{{ $funcionario->perfil ?? 'Enfermeiro' }}" disabled
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 text-sm outline-none cursor-not-allowed">
                </div>

                <div class="border-t border-slate-100 pt-5 flex justify-end">
                    <a href="{{ route('funcionarios.edit', $funcionario) }}"
                        class="px-6 py-2.5 bg-cyan-500 text-white font-semibold rounded-full hover:bg-cyan-600 transition text-sm shadow-sm text-center">
                        Editar informações
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
