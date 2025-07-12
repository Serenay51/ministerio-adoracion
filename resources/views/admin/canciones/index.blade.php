<x-app-layout>
    <div class="container my-5">
        {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Canciones</li>
            </ol>
        </nav>
        <h2 class="mb-4">Gestión de Canciones</h2>

        {{-- Botón Crear --}}
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearModal">
            <i class="bi bi-plus-lg"></i> Crear Canción
        </button>

        {{-- Barra de Búsqueda --}}
        <form method="GET" action="{{ route('admin.canciones.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por título o autor">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>

        {{-- Tabla Canciones --}}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($canciones as $cancion)
                <tr>
                    <td>{{ $cancion->titulo }}</td>
                    <td>{{ $cancion->autor ?? '-' }}</td>
                    <td>
                        {{-- Botón editar --}}
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarModal"
                            data-id="{{ $cancion->id }}" data-titulo="{{ $cancion->titulo }}" data-autor="{{ $cancion->autor }}">
                            <i class="bi bi-pencil"></i> Editar
                        </button>

                        {{-- Form eliminar --}}
                        <form action="{{ route('admin.canciones.eliminar', $cancion->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar canción?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $canciones->withQueryString()->links() }}
        </div>
        

        {{-- Modal Crear --}}
        <div class="modal fade" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.canciones.crear') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearModalLabel">Crear Canción</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="autor" class="form-label">Autor</label>
                            <input type="text" name="autor" id="autor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="letra" class="form-label">Letra</label>
                            <textarea name="letra" id="letra" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Editar --}}
        <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" class="modal-content" id="formEditar">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarModalLabel">Editar Canción</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editarTitulo" class="form-label">Título</label>
                            <input type="text" name="titulo" id="editarTitulo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarAutor" class="form-label">Autor</label>
                            <input type="text" name="autor" id="editarAutor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="editarLetra" class="form-label">Letra</label>
                            <textarea name="letra" id="editarLetra" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
        <script>
            var editarModal = document.getElementById('editarModal');
            editarModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var titulo = button.getAttribute('data-titulo');
                var autor = button.getAttribute('data-autor');

                // Para la letra hay que pedirla por ajax o ponerla como data-attribute si la querés en el modal, te dejo ejemplo básico sin letra.

                var modal = this;
                modal.querySelector('#editarTitulo').value = titulo;
                modal.querySelector('#editarAutor').value = autor;

                var form = modal.querySelector('#formEditar');
                form.action = `/admin/canciones/${id}`;
            });
        </script>
        @endpush

    </div>
</x-app-layout>
