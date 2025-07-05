<x-app-layout>
    <div class="container mx-auto py-6 px-4">

        {{-- Título y fecha --}}
        <h1 class="text-3xl font-bold mb-2">{{ $culto->descripcion }}</h1>
        <p class="text-gray-500 mb-6">Fecha: {{ $culto->fecha->format('d/m/Y') }}</p>

        {{-- Botón para asignar miembros y roles (solo presidente) --}}
        @if(Auth::user()->is_president)
            <a href="{{ route('admin.cultos.asignar', $culto) }}" class="btn btn-primary mb-6">
                <i class="bi bi-person-lines-fill me-1"></i> Asignar miembros y roles
            </a>
        @endif

                @php
                    $esPresidente = Auth::user()->is_president;
                    $esDirector = $culto->rolCultos->where('rol', 'director')->pluck('user_id')->contains(Auth::id());
                    $esMusico = $culto->rolCultos->where('rol', 'musico')->pluck('user_id')->contains(Auth::id());
                @endphp

        {{-- Director --}}
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Director</h2>
            @php
                $directores = $culto->rolCultos->where('rol', 'director');
                $esDirector = $directores->pluck('user_id')->contains(Auth::id());
                $puedeOrdenar = Auth::user()->is_president || $esDirector;
            @endphp
            @if ($directores->isEmpty())
                <p class="text-gray-500">No asignado</p>
            @else
                <ul class="list-unstyled ms-1 text-gray-800">
                    @foreach ($directores as $dir)
                        <li><i class="bi bi-person-badge me-1"></i> {{ $dir->user->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Músicos --}}
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Músicos</h2>
            @php
                $musicos = $culto->rolCultos->where('rol', 'musico');
            @endphp
            @if($musicos->isNotEmpty())
                <ul class="list-unstyled ms-1 text-gray-800">
                    @foreach($musicos as $musico)
                        @php
                            $instrumento = strtolower($musico->instrumento ?? '');
                            $icono = match($instrumento) {
                                'bateria' => 'fas fa-drum',
                                'guitarra' => 'fas fa-guitar',
                                'teclado' => 'fas fa-keyboard',
                                'bajo' => 'fas fa-record-vinyl',
                                default => 'fas fa-music',
                            };
                        @endphp
                        <li>
                            <i class="{{ $icono }} me-1"></i>
                            {{ $musico->user->name }}
                            @if($instrumento)
                                <small class="text-muted">({{ ucfirst($instrumento) }})</small>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No asignados.</p>
            @endif
        </div>
        

        {{-- Coro de Apoyo --}}
        <div class="mb-4">
            <h2 class="text-xl font-semibold">Coro de Apoyo</h2>
            @php
                $coristas = $culto->rolCultos->where('rol', 'coro_apoyo');
            @endphp
            @if($coristas->isNotEmpty())
                <ul class="list-unstyled ms-1 text-gray-800">
                    @foreach($coristas as $corista)
                        <li><i class="bi bi-mic-fill me-1"></i> {{ $corista->user->name }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No asignados.</p>
            @endif
        </div>

        {{-- Botón para asignar canción (presidente o director) --}}
        @if($puedeOrdenar)
            <div class="mb-4">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#asignarCancionModal">
                    <i class="bi bi-music-note-plus me-1"></i> Asignar canción
                </button>
            </div>
        @endif

        {{-- Canciones --}}
        <div id="canciones-lista">
            @forelse($culto->canciones as $cancion)
                <div class="cancion-item border rounded p-3 mb-2 bg-white shadow-sm flex justify-between items-center"
                     data-id="{{ $cancion->id }}"
                     @if($puedeOrdenar) draggable="true" style="cursor: grab;" @endif>
                    <div class="flex items-center gap-2">
                        @if($puedeOrdenar)
                            <i class="bi bi-grip-vertical cursor-move text-gray-500"></i>
                        @endif
                        <div>
                            <div class="font-bold">{{ $cancion->titulo }}</div>
                            <button class="btn btn-sm btn-outline-primary mt-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#estructuraModal"
                                    data-titulo="{{ $cancion->titulo }}"
                                    data-estructura="{{ $cancion->pivot->estructura }}"
                                    data-tonalidad="{{ $cancion->pivot->tonalidad }}">
                                Ver
                            </button>
                        </div>
                    </div>
                    @if($puedeOrdenar)
                        <form action="{{ route('cultos.canciones.destroy', [$culto, $cancion]) }}" method="POST" class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Seguro que querés eliminar esta canción?')">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-gray-500">No hay canciones asignadas.</p>
            @endforelse
        </div>
        
        <div id="alerta-orden" class="alert alert-success alert-dismissible mt-3" role="alert"
            style="opacity: 0; transition: opacity 0.8s ease; pointer-events: none;">
            <i class="bi bi-check-circle me-1"></i> El orden de las canciones fue actualizado.
        </div>

        {{-- Volver --}}
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">← Volver al calendario</a>
        </div>
    </div>

    {{-- Modal Detalle Canción --}}
    <div class="modal fade" id="estructuraModal" tabindex="-1" aria-labelledby="estructuraModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="estructuraModalLabel">Detalle de Canción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Canción:</strong> <span id="modal-titulo"></span></p>

                    @if($puedeOrdenar)
                        {{-- Editable solo para presidente/director --}}
                        <label for="modal-estructura-input"><strong>Estructura:</strong></label>
                        <textarea id="modal-estructura-input" class="form-control" rows="4" placeholder="Ingresar estructura..."></textarea>
                        <div id="estructura-guardado-msg" class="text-success small mt-1" style="display:none;">Guardado correctamente</div>
                    @else
                        {{-- Solo lectura para otros usuarios --}}
                        <p>
                            <strong>Estructura:</strong>
                            <pre id="modal-estructura" class="text-gray-600 fst-italic"></pre>
                        </p>
                    @endif
                    <p>
                        <strong>Tonalidad:</strong>
                        <span id="modal-tonalidad" class="text-gray-600 fst-italic"></span>
                        <div id="tonalidad-guardado-msg" class="text-success small mt-1" style="display:none;">Guardado correctamente</div>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Asignar Canción --}}
    <div class="modal fade" id="asignarCancionModal" tabindex="-1" aria-labelledby="asignarCancionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('cultos.canciones.asignar', $culto) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="asignarCancionModalLabel">Asignar Canción al Culto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cancion_id" class="form-label">Canción</label>
                        <select name="cancion_id" id="cancion_id" class="form-select" required>
                            <option value="">-- Seleccioná una canción --</option>
                            @foreach($cancionesDisponibles as $cancion)
                                <option value="{{ $cancion->id }}">{{ $cancion->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="info-cancion" class="border p-2 mb-3" style="display:none;">
                        <p><strong>Autor:</strong> <span id="autor-cancion"></span></p>
                        <p><strong>Vista previa de la letra:</strong></p>
                        <pre id="preview-letra" style="white-space: pre-wrap; max-height: 150px; overflow-y: auto;"></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Asignar</button>
                </div>
            </form>
        </div>
    </div>
    @php
        $cancionesJson = $cancionesDisponibles->map(function($c) {
            return [
                'id' => $c->id,
                'titulo' => $c->titulo,
                'autor' => $c->autor ?? 'Desconocido',
                'letra' => $c->letra ?? 'No hay letra disponible.',
            ];
        });
    @endphp

    @push('styles')
        <style>
            .cancion-item {
                cursor: grab;
            }
            .cancion-item:active {
                cursor: grabbing;
            }
        </style>
    @endpush

            @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canciones = @json($cancionesJson);
            const selectCancion = document.getElementById('cancion_id');
            const infoDiv = document.getElementById('info-cancion');
            const autorSpan = document.getElementById('autor-cancion');
            const previewDiv = document.getElementById('preview-letra');

            if (selectCancion) {
                selectCancion.addEventListener('change', function () {
                    const id = parseInt(this.value);
                    const cancion = canciones.find(c => c.id === id);
                    if (cancion) {
                        autorSpan.textContent = cancion.autor;
                        previewDiv.textContent = cancion.letra;
                        infoDiv.style.display = 'block';
                    } else {
                        infoDiv.style.display = 'none';
                        autorSpan.textContent = '';
                        previewDiv.textContent = '';
                    }
                });
            }

            var estructuraModal = document.getElementById('estructuraModal');
            var modalTitulo = document.getElementById('modal-titulo');
            var modalEstructuraSpan = document.getElementById('modal-estructura');
            var modalTonalidadSpan = document.getElementById('modal-tonalidad');
            var estructuraInput = document.getElementById('modal-estructura-input');
            var guardadoMsg = document.getElementById('estructura-guardado-msg');

            if (estructuraModal) {
                estructuraModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var titulo = button.getAttribute('data-titulo');
                    var estructura = button.getAttribute('data-estructura');
                    var tonalidad = button.getAttribute('data-tonalidad');

                    if (modalTitulo) modalTitulo.textContent = titulo;
                    if (modalTonalidadSpan) modalTonalidadSpan.textContent = tonalidad ? tonalidad : 'Todavía no definida';

                    @if($puedeOrdenar)
                        if (estructuraInput) {
                            var cancionInfo = canciones.find(c => c.titulo === titulo);
                            var letra = cancionInfo?.letra ?? '';
                            estructuraInput.value = estructura || letra;
                            if (guardadoMsg) guardadoMsg.style.display = 'none';
                            if (modalEstructuraSpan) modalEstructuraSpan.style.display = 'none';
                            estructuraInput.style.display = 'block';
                        }
                    @else
                        if (modalEstructuraSpan) {
                            modalEstructuraSpan.textContent = estructura ? estructura : 'Todavía no definida';
                            modalEstructuraSpan.style.display = 'inline';
                        }
                        if (estructuraInput) estructuraInput.style.display = 'none';
                        if (guardadoMsg) guardadoMsg.style.display = 'none';
                    @endif
                });
            }

            @if($puedeOrdenar)
            if (estructuraInput) {
                estructuraInput.addEventListener('blur', guardarEstructura);
            }

            function guardarEstructura() {
                if (!estructuraInput || !modalTitulo) return;

                var nuevaEstructura = estructuraInput.value.trim();
                var cancionTitulo = modalTitulo.textContent;

                var cancionDiv = Array.from(document.querySelectorAll('.cancion-item')).find(el => {
                    return el.querySelector('.font-bold')?.textContent === cancionTitulo;
                });
                if (!cancionDiv) return;

                var cancionId = cancionDiv.dataset.id;
                var botonVer = cancionDiv.querySelector('button[data-bs-target="#estructuraModal"]');

                fetch("{{ route('cultos.canciones.actualizarEstructura', $culto) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    credentials: "same-origin", 
                    body: JSON.stringify({
                        cancion_id: cancionId,
                        estructura: nuevaEstructura
                    })
                }).then(res => {
                    if (res.ok) {
                        if (guardadoMsg) guardadoMsg.style.display = 'block';
                        if (botonVer) botonVer.setAttribute('data-estructura', nuevaEstructura);
                        if (modalEstructuraSpan) modalEstructuraSpan.textContent = nuevaEstructura;

                        setTimeout(() => {
                            if (guardadoMsg) guardadoMsg.style.display = 'none';
                        }, 2000);
                    } else {
                        alert('Error al guardar la estructura');
                    }
                });
            }
            @endif
        });
        </script>

        <script>
        @if(Auth::user()->tieneRolEnCulto($culto->id, 'musico'))
        var modalTonalidadSpan = document.getElementById('modal-tonalidad');
        var tonalidadInput = document.createElement('input');
        tonalidadInput.type = 'text';
        tonalidadInput.className = 'form-control mt-1';
        tonalidadInput.placeholder = 'Proponer tonalidad';

        if (modalTonalidadSpan && modalTonalidadSpan.parentNode) {
            modalTonalidadSpan.parentNode.appendChild(tonalidadInput);

            tonalidadInput.addEventListener('blur', guardarTonalidad);
            tonalidadInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    tonalidadInput.blur();
                }
            });

            function guardarTonalidad() {
                var nuevaTonalidad = tonalidadInput.value.trim();
                var modalTitulo = document.getElementById('modal-titulo');
                if (!modalTitulo) return;

                var cancionTitulo = modalTitulo.textContent;

                var cancionDiv = Array.from(document.querySelectorAll('.cancion-item')).find(el => {
                    return el.querySelector('.font-bold')?.textContent === cancionTitulo;
                });
                if (!cancionDiv) return;

                var cancionId = cancionDiv.dataset.id;

                var tonalidadGuardadoMsg = document.getElementById('tonalidad-guardado-msg');

                fetch("{{ route('cultos.canciones.actualizarTonalidad', $culto) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        cancion_id: cancionId,
                        tonalidad_propuesta: nuevaTonalidad
                    })
                }).then(res => {
                    if (res.ok) {
                        if (modalTonalidadSpan) modalTonalidadSpan.textContent = nuevaTonalidad;
                        tonalidadInput.value = nuevaTonalidad;

                        if (tonalidadGuardadoMsg) {
                            tonalidadGuardadoMsg.style.display = 'block';
                            setTimeout(() => tonalidadGuardadoMsg.style.display = 'none', 2000);
                        }
                    } else {
                        alert('Error al guardar la tonalidad');
                    }
                });
            }

            estructuraModal?.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var tonalidad = button.getAttribute('data-tonalidad');
                tonalidadInput.value = tonalidad || '';
            });
        }
        @endif
        </script>

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if($puedeOrdenar)
            const container = document.getElementById('canciones-lista');

            if (container) {
                Sortable.create(container, {
                    animation: 350,
                    onEnd: function () {
                        const orden = Array.from(container.children).map(el => el.dataset.id);
                        fetch("{{ route('cultos.canciones.reordenar', $culto) }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ orden })
                        }).then(res => {
                            if (!res.ok) {
                                alert('Error al guardar el orden');
                            } else {
                                const alerta = document.getElementById('alerta-orden');
                                if (alerta) {
                                    alerta.style.pointerEvents = 'auto';
                                    alerta.style.opacity = '1';
                                    setTimeout(() => {
                                        alerta.style.opacity = '0';
                                        alerta.style.pointerEvents = 'none';
                                    }, 2500);
                                }
                            }
                        });
                    }
                });
            }
            @endif
        });
        </script>
        @endpush


</x-app-layout>
