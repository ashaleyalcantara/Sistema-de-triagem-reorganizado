<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tria')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body
    class="@yield('body-class', 'bg-sky-50 text-slate-700 font-sans antialiased min-h-screen')"
    @yield('body-attributes')>

    @include('components.header')

    <main class="@yield('main-class', 'max-w-6xl mx-auto p-6 space-y-6')">
        @include('components.flash-messages')
        @yield('content')
    </main>

    @hasSection('footer')
        @yield('footer')
    @endif

    @stack('scripts')
</body>

</html>
