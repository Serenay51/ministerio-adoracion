<x-app-layout>
    <div class="container py-6 px-4">
        <h1 class="text-3xl font-bold mb-6">Asignar miembros y roles - {{ $culto->descripcion }}</h1>
        <p class="mb-4">Fecha: {{ $culto->fecha->format('d/m/Y') }}</p>

        <form action="{{ route('admin.cultos.asignar.update', $culto) }}" method="POST">
            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Miembro</th>
                        <th>Director</th>
                        <th>Músico</th>
                        <th>Instrumento</th>
                        <th>Coro de Apoyo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($miembros as $index => $miembro)
                        @php
                            $rolesAsignados = $asignaciones->where('user_id', $miembro->id)->pluck('rol')->toArray();
                            $instrumentoAsignado = optional($asignaciones->where('user_id', $miembro->id)->where('rol', 'musico')->first())->instrumento ?? '';
                            $tieneRoles = count($rolesAsignados) > 0;
                        @endphp
                        <tr @if(!$tieneRoles) class="table-warning" @endif>
                            <td>{{ $miembro->name }}</td>

                            {{-- Director --}}
                            <td class="text-center">
                                <input type="checkbox" name="asignaciones[{{ $index }}][roles][]" value="director"
                                    {{ in_array('director', $rolesAsignados) ? 'checked' : '' }}>
                                <input type="hidden" name="asignaciones[{{ $index }}][user_id]" value="{{ $miembro->id }}">
                            </td>

                            {{-- Músico --}}
                            <td class="text-center">
                                <input type="checkbox" class="rol-musico" data-index="{{ $index }}"
                                    name="asignaciones[{{ $index }}][roles][]" value="musico"
                                    {{ in_array('musico', $rolesAsignados) ? 'checked' : '' }}>
                            </td>

                            {{-- Instrumento --}}
                            <td>
                                <select name="asignaciones[{{ $index }}][instrumento]" class="form-select instrumento-select"
                                    id="instrumento-{{ $index }}"
                                    style="{{ in_array('musico', $rolesAsignados) ? '' : 'display: none;' }}">
                                    <option value="">--</option>
                                    <option value="bateria" {{ $instrumentoAsignado === 'bateria' ? 'selected' : '' }}>Batería</option>
                                    <option value="guitarra" {{ $instrumentoAsignado === 'guitarra' ? 'selected' : '' }}>Guitarra</option>
                                    <option value="teclado" {{ $instrumentoAsignado === 'teclado' ? 'selected' : '' }}>Teclado</option>
                                    <option value="bajo" {{ $instrumentoAsignado === 'bajo' ? 'selected' : '' }}>Bajo</option>
                                </select>
                            </td>

                            {{-- Coro de Apoyo --}}
                            <td class="text-center">
                                <input type="checkbox" name="asignaciones[{{ $index }}][roles][]" value="coro_apoyo"
                                    {{ in_array('coro_apoyo', $rolesAsignados) ? 'checked' : '' }}>
                            </td>

                            {{-- Estado --}}
                            <td class="text-center">
                                @if(!$tieneRoles)
                                    <small class="text-danger">No Asignado</small>
                                @else
                                    <small class="text-success">Asignado</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Guardar Asignaciones</button>
                <a href="{{ route('cultos.show', $culto) }}" class="btn btn-secondary ms-2">Cancelar</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.rol-musico');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    const index = this.dataset.index;
                    const select = document.getElementById('instrumento-' + index);
                    if (this.checked) {
                        select.style.display = '';
                    } else {
                        select.style.display = 'none';
                        select.value = '';
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
