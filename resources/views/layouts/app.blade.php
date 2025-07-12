<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Ministerio de Adoración') }}</title>

    {{-- TailwindCSS (via Breeze) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow mb-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="text-xl font-bold">
                <a href="{{ route('dashboard') }}"><img style="max-height: 60px;" src="{{ asset('images/logo.png') }}" alt="Ministerio de Adoración"></a>
            </div>
            <div>
                @auth
                    <span class="me-3">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Cerrar sesión</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ACCESOS DEL PRESIDENTE --}}
    @auth
        @if(Auth::user()->is_president)
            <div class="bg-gray-200 py-2 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 flex gap-4">
                   <i class="fas fa-home"></i> <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">Inicio</a>
                   <i class="fas fa-users"></i> <a href="{{ route('admin.usuarios.index') }}" class="text-sm text-gray-600 hover:underline">Miembros</a>
                   <i class="fas fa-music"></i> <a href="{{ route('admin.canciones.index') }}" class="text-sm text-gray-600 hover:underline">Canciones</a>
                   <i class="fas fa-church"></i> <a href="{{ route('admin.cultos.index') }}" class="text-sm text-gray-600 hover:underline">Cultos</a>
                </div>
            </div>
        @endif
    @endauth

    {{-- CONTENIDO --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fi-icons@1.0.0/css/fi.css">

    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

    {{-- Scripts adicionales --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>




    {{-- Stack scripts personalizados --}}
    @stack('scripts')

</body>
<footer class="bg-gray-300 shadow mt-4">
    <div class="max-w-7xl mx-auto px-4 py-2 text-center text-gray-700">
        &copy; {{ date('Y') }} Ministerio De Adoración - Iglesia Evangélica Bautista de Liniers. Todos los derechos reservados.
        <br>
        <span class="text-xs">Desarrollado por <a href="https://github.com/Serenay51" class="hover:underline">Lautaro Arana</a></span>
    </div>
</footer>
</html>
