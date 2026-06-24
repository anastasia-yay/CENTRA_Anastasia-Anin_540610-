<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- <style> --}}
        {{-- /* =======================================================
           CENTRA FULLSCREEN BACKGROUND
        ======================================================= */

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        .centra-auth-body {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 2rem;

            background:
                linear-gradient(
                    rgba(50,120,180,.15),
                    rgba(50,120,180,.15)
                ),
                url('{{ asset("images/bg-centra.jpg") }}');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .centra-auth-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        } --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    {{-- </style> --}}
</head>

<body class="centra-auth-body">

    <div class="centra-auth-container">
        @yield('content')
    </div>

</body>
</html>
