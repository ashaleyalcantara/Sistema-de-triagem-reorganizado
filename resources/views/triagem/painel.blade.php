@extends('layouts.app')

@section('title', 'Tria - Painel de Triagem')
@section('main-class', 'max-w-7xl mx-auto p-6 md:p-10 space-y-6')

@section('content')
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Avaliação e classificação de risco</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <div class="md:col-span-5 space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <x-stat-card label="Aguardando Triagem" :value="$pacientesNaFila ?? 0" />
                <x-stat-card label="Triados Hoje" :value="$triadosHoje ?? 0" />
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                @include('triagem.listar')
            </div>
        </div>

        <div class="md:col-span-7 bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            @include('triagem.cadastrar')
        </div>
    </div>
@endsection
