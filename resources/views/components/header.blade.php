@php
    $usuario = auth()->user();
    $perfil = $usuario?->perfil;

    $rotaInicial = match ($perfil) {
        'Médico' => route('medico.index'),
        'Recepcionista' => route('recepcionista'),
        'Enfermeiro' => route('triagem.index'),
        'Administrador' => route('admin'),
        default => url('/'),
    };
@endphp

<header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center">
    <a href="{{ $rotaInicial }}" class="flex items-center space-x-2 text-cyan-600 font-bold text-xl">
        <img src="{{ asset('images/LogoOficial.svg') }}" alt="Logo Tria" class="w-8 h-8 object-contain">
        <span>Tria</span>
    </a>

    <nav class="flex items-center space-x-6 text-sm font-medium text-slate-600">
        @if ($perfil === 'Médico')
            <a href="{{ route('medico.index') }}"
                class="{{ request()->routeIs('medico.*') ? 'text-cyan-600 font-semibold' : 'hover:text-cyan-600 transition' }}">
                Consulta
            </a>
        @elseif ($perfil === 'Recepcionista')
            <a href="{{ route('recepcionista') }}"
                class="{{ request()->routeIs('recepcionista') ? 'text-cyan-600 font-semibold' : 'hover:text-cyan-600 transition' }}">
                Cadastro de Paciente
            </a>
        @elseif ($perfil === 'Enfermeiro')
            <a href="{{ route('triagem.index') }}"
                class="{{ request()->routeIs('triagem.*') ? 'text-cyan-600 font-semibold' : 'hover:text-cyan-600 transition' }}">
                Triagem
            </a>
        @elseif ($perfil === 'Administrador')
            <a href="{{ route('admin') }}"
                class="{{ request()->routeIs('admin') ? 'text-cyan-600 font-semibold' : 'hover:text-cyan-600 transition' }}">
                Funcionários
            </a>
        @endif

        <a href="{{ route('funcionarios.perfil') }}"
            class="border border-cyan-500 text-cyan-600 px-4 py-1.5 rounded-full flex items-center space-x-2 bg-cyan-50 transition h-[34px]">
            <i class="fa-regular fa-user"></i>
            <span>Perfil</span>
        </a>

        <form action="{{ route('logout') }}" method="POST" class="flex items-center m-0 p-0">
            @csrf
            <button type="submit"
                class="bg-cyan-500 text-white px-4 py-1.5 rounded-full flex items-center space-x-2 hover:bg-cyan-600 transition h-[34px] leading-none">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Sair</span>
            </button>
        </form>
    </nav>
</header>
