<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tria')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="@yield('body-class', 'bg-sky-50 text-slate-700 font-sans antialiased min-h-screen')">
    @yield('content')

    @stack('scripts')
</body>

</html>
