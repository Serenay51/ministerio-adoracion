{{-- resources/views/admin/index.blade.php --}}

<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-4xl font-bold mb-8 text-center">Panel de Administración</h1>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <a href="{{ route('admin.usuarios') }}" class="btn btn-primary text-center py-6">
                <i class="bi bi-people-fill text-3xl mb-2"></i>
                <div class="text-lg font-semibold">Gestionar Miembros</div>
            </a>

            <a href="{{ route('admin.cultos') }}" class="btn btn-success text-center py-6">
                <i class="bi bi-calendar-event-fill text-3xl mb-2"></i>
                <div class="text-lg font-semibold">Gestionar Cultos</div>
            </a>

            <a href="{{ route('admin.canciones') }}" class="btn btn-info text-center py-6">
                <i class="bi bi-music-note-list text-3xl mb-2"></i>
                <div class="text-lg font-semibold">Gestionar Canciones</div>
            </a>
        </div>
    </div>
</x-app-layout>
