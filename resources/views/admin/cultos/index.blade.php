<x-app-layout>
    <div class="container my-5">
        {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cultos</li>
            </ol>
        </nav>
        <h1 class="mb-4">Cultos</h1>

        {{-- Botón para abrir modal Crear --}}
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createCultoModal">
            Agregar Culto
        </button>

        {{-- Barra de búsqueda --}}
        <form method="GET" action="{{ route('admin.cultos.index') }}" class="mb-3">
            <div class="input-group">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por descripción">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>
        </form>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tabla cultos --}}
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th style="width: 160px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cultos as $culto)
                    <tr>
                        <td>{{ $culto->fecha->format('d/m/Y') }}</td>
                        <td>{{ $culto->descripcion }}</td>
                        <td>
                            <button 
                                class="btn btn-sm btn-outline-primary me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editCultoModal"
                                data-id="{{ $culto->id }}"
                                data-fecha="{{ $culto->fecha->format('Y-m-d') }}"
                                data-descripcion="{{ $culto->descripcion }}"
                            >
                                Editar
                            </button>

                            <form action="{{ route('admin.cultos.eliminar', $culto) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro querés eliminar este culto?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $cultos->withQueryString()->links() }}
        </div>

        {{-- Modal Crear Culto --}}
        <div class="modal fade" id="createCultoModal" tabindex="-1" aria-labelledby="createCultoModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form action="{{ route('admin.cultos.crear') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCultoModalLabel">Agregar Culto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="createFecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="createFecha" name="fecha" required value="{{ old('fecha') }}">
                        @error('fecha')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="createDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="createDescripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Culto</button>
                </div>
            </form>
          </div>
        </div>

        {{-- Modal Editar Culto --}}
        <div class="modal fade" id="editCultoModal" tabindex="-1" aria-labelledby="editCultoModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="editCultoForm" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editCultoModalLabel">Editar Culto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editFecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="editFecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Culto</button>
                </div>
            </form>
          </div>
        </div>
    </div>

    {{-- Bootstrap 5 JS (en caso de que no lo tengas ya cargado) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const editCultoModal = document.getElementById('editCultoModal')
        editCultoModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget
            const id = button.getAttribute('data-id')
            const fecha = button.getAttribute('data-fecha')
            const descripcion = button.getAttribute('data-descripcion')

            const modalTitle = editCultoModal.querySelector('.modal-title')
            const inputFecha = editCultoModal.querySelector('#editFecha')
            const inputDescripcion = editCultoModal.querySelector('#editDescripcion')
            const form = editCultoModal.querySelector('form')

            modalTitle.textContent = `Editar Culto: ${fecha}`
            inputFecha.value = fecha
            inputDescripcion.value = descripcion
            form.action = `/admin/cultos/${id}`
        })
    </script>
</x-app-layout>
