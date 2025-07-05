<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Culto;
use Illuminate\Http\Request;
use App\Models\Cancion;

class CultoCancionController extends Controller
{

    public function show(Culto $culto)
    {
        $culto->load('canciones');

        $idsAsignadas = $culto->canciones->pluck('id')->toArray();
        $cancionesDisponibles = Cancion::whereNotIn('id', $idsAsignadas)->orderBy('titulo')->get();

        return view('cultos.show', compact('culto', 'cancionesDisponibles'));
    }

    public function store(Request $request, Culto $culto)
    {
        try {
            $validated = $request->validate([
                'cancion_id' => 'required|exists:cancions,id',
            ]);

            if ($culto->canciones()->where('cancion_id', $validated['cancion_id'])->exists()) {
                return redirect()->back()->with('error', 'La canción ya fue asignada a este culto.');
            }

            $culto->canciones()->attach($validated['cancion_id']);

            return redirect()->route('cultos.show', $culto)->with('success', 'Canción asignada correctamente.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al asignar canción',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function destroy(Culto $culto, Cancion $cancion)
    {
        $culto->canciones()->detach($cancion->id);

        return redirect()->route('cultos.show', $culto)->with('success', 'Canción eliminada del culto.');
    }

    public function reordenar(Request $request, Culto $culto)
    {
        $data = $request->validate([
            'orden' => 'required|array',
            'orden.*' => 'integer',
        ]);

        foreach ($data['orden'] as $index => $cancionId) {
            $culto->canciones()->updateExistingPivot($cancionId, ['orden' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function actualizarEstructura(Request $request, Culto $culto)
    {
        $data = $request->validate([
            'cancion_id' => 'required|integer|exists:cancions,id',
            'estructura' => 'nullable|string|max:1000',
        ]);

        // Validar que el usuario sea presidente o director del culto
        $user = $request->user();
        $esPresidente = $user->is_president;
        $esDirector = $culto->rolCultos()
            ->where('user_id', $user->id)
            ->where('rol', 'director')
            ->exists();

        if (!($esPresidente || $esDirector)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Verificar que la canción esté asignada al culto
        $pivot = $culto->canciones()->where('cancion_id', $data['cancion_id'])->first();

        if (!$pivot) {
            return response()->json(['error' => 'Canción no asignada a este culto'], 404);
        }

        // Actualizar solo la estructura en la tabla pivot
        $culto->canciones()->updateExistingPivot($data['cancion_id'], [
            'estructura' => $data['estructura'],
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function actualizarTonalidad(Request $request, Culto $culto)
    {
        $data = $request->validate([
            'cancion_id' => 'required|integer|exists:cancions,id',
            'tonalidad_propuesta' => 'required|string|max:50',
        ]);

        // Validar que el usuario sea presidente o musico del culto
        $user = $request->user();
        $esPresidente = $user->is_president;
        $esMusico = $culto->rolCultos()
            ->where('user_id', $user->id)
            ->where('rol', 'musico')
            ->exists();

        if (!($esPresidente || $esMusico)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Verificar que la canción esté asignada al culto
        $pivot = $culto->canciones()->where('cancion_id', $data['cancion_id'])->first();

        if (!$pivot) {
            return response()->json(['error' => 'Canción no asignada a este culto'], 404);
        }

        // Actualizar solo la tonalidad en la tabla pivot
        $culto->canciones()->updateExistingPivot($data['cancion_id'], [
            'tonalidad' => $data['tonalidad_propuesta'],
        ]);

        return response()->json(['status' => 'ok']);
    }
    

}
