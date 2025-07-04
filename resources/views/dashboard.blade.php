<x-app-layout>
    <div class="container mx-auto px-4 py-6">

        {{-- Título + navegación de meses --}}
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('dashboard.date', ['year' => $fecha->copy()->subMonth()->year, 'month' => $fecha->copy()->subMonth()->month]) }}"
                class="text-2xl text-gray-600 hover:text-blue-600">&larr;</a>

            <h1 class="text-3xl font-bold text-center">
                Cultos de {{ $fecha->translatedFormat('F Y') }}
            </h1>

            <a href="{{ route('dashboard.date', ['year' => $fecha->copy()->addMonth()->year, 'month' => $fecha->copy()->addMonth()->month]) }}"
                class="text-2xl text-gray-600 hover:text-blue-600">&rarr;</a>
        </div>

        {{-- Días de la semana --}}
        <div class="grid grid-cols-7 gap-4 text-center text-gray-700 font-semibold mb-2">
            @foreach(['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                <div>{{ $dia }}</div>
            @endforeach
        </div>

        @php
            $fecha->locale('es');

            $inicioMes = $fecha->copy()->startOfMonth();
            $finMes = $fecha->copy()->endOfMonth();

            $inicioCuadricula = $inicioMes->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
            $finCuadricula = $finMes->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);

            $hoy = now()->startOfDay();
        @endphp

        {{-- Calendario --}}
        <div class="grid grid-cols-7 gap-4">
            @for ($date = $inicioCuadricula->copy(); $date->lte($finCuadricula); $date->addDay())
                @php
                    $cultoDelDia = $cultos->first(function ($culto) use ($date) {
                        return $culto->fecha->format('Y-m-d') === $date->format('Y-m-d');
                    });
                    $esDelMes = $date->month === $fecha->month;
                    $esHoy = $date->equalTo($hoy);
                @endphp

                <div class="rounded-xl p-2 h-32 border flex flex-col justify-between transition-transform duration-200 ease-in-out
                    {{ $esHoy ? 'bg-blue-100 border-blue-600 text-blue-900' : 'bg-white border-gray-200' }}
                    {{ !$esDelMes ? 'bg-gray-100 text-gray-400' : '' }}
                    hover:scale-105 hover:shadow-md">

                    <div class="text-sm font-bold">{{ $date->day }}</div>

                    @if($cultoDelDia)
                        <div class="text-xs text-green-600 font-semibold truncate">
                            {{ Str::limit($cultoDelDia->descripcion, 20) }}
                        </div>
                        <div class="text-xs truncate">
                            Dir: {{ optional($cultoDelDia->rolCultos->firstWhere('rol', 'director'))->user->name ?? '—' }}
                        </div>
                        <a href="{{ route('cultos.show', $cultoDelDia) }}" class="text-blue-500 text-xs hover:underline">Ver</a>
                    @endif
                </div>
            @endfor
        </div>
    </div>
</x-app-layout>
