<x-app-layout>
    <div class="container my-5">
                {{-- Breadcrumbs --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Miembros</li>
            </ol>
        </nav>
        <h1 class="mb-4">Miembros del Ministerio</h1>



        {{-- Botón para abrir modal Crear --}}
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
            Agregar Miembro
        </button>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tabla usuarios --}}
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th style="width: 160px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <button 
                                class="btn btn-sm btn-outline-primary me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                            >
                                Editar
                            </button>

                            <form action="{{ route('admin.usuarios.eliminar', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro querés eliminar este miembro?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Modal Crear Miembro --}}
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form action="{{ route('admin.usuarios.crear') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Agregar Miembro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="createName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="createName" name="name" required value="{{ old('name') }}">
                        @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="createEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="createEmail" name="email" required value="{{ old('email') }}">
                        @error('email')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="createPassword" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="createPassword" name="password" required>
                        @error('password')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="createPasswordConfirm" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="createPasswordConfirm" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Miembro</button>
                </div>
            </form>
          </div>
        </div>

        {{-- Modal Editar Miembro --}}
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="editForm" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Miembro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">Nueva Contraseña (opcional)</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="editPasswordConfirm" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="editPasswordConfirm" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
          </div>
        </div>
    </div>

    {{-- Bootstrap 5 JS (si no lo tenés ya incluido) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const editModal = document.getElementById('editModal')
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget
            const id = button.getAttribute('data-id')
            const name = button.getAttribute('data-name')
            const email = button.getAttribute('data-email')

            const modalTitle = editModal.querySelector('.modal-title')
            const inputName = editModal.querySelector('#editName')
            const inputEmail = editModal.querySelector('#editEmail')
            const form = editModal.querySelector('form')

            modalTitle.textContent = `Editar Miembro: ${name}`
            inputName.value = name
            inputEmail.value = email
            form.action = `/admin/usuarios/${id}`
        })
    </script>
</x-app-layout>
