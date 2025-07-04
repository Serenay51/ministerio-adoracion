<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Culto;
use App\Models\User;
use App\Models\RolCulto;

class AsignacionController extends Controller
{
    public function edit(Culto $culto)
    {
        // Traer todos los usuarios para asignar
        $miembros = User::orderBy('name')->get();

        // Roles disponibles
        $roles = ['director', 'musico', 'coro_apoyo'];

        // Cargar asignaciones actuales
        $asignaciones = $culto->rolCultos()->with('user')->get();

        return view('admin.cultos.asignar', compact('culto', 'miembros', 'roles', 'asignaciones'));
    }

    public function update(Request $request, Culto $culto)
    {
        $data = $request->validate([
            'asignaciones' => 'array',
            'asignaciones.*.user_id' => 'required|exists:users,id',
            'asignaciones.*.roles' => 'nullable|array',
            'asignaciones.*.roles.*' => 'in:director,musico,coro_apoyo',
            'asignaciones.*.instrumento' => 'nullable|string|max:255',
        ]);

        // Eliminar asignaciones viejas
        $culto->rolCultos()->delete();

        // Insertar nuevas asignaciones
        foreach ($data['asignaciones'] as $asignacion) {
            if (!isset($asignacion['roles']) || count($asignacion['roles']) === 0) {
                continue; // Si no tiene ningún rol marcado, no se asigna
            }

            foreach ($asignacion['roles'] as $rol) {
                RolCulto::create([
                    'culto_id' => $culto->id,
                    'user_id' => $asignacion['user_id'],
                    'rol' => $rol,
                    'instrumento' => $rol === 'musico' ? ($asignacion['instrumento'] ?? null) : null,
                ]);
            }
        }

        return redirect()->route('cultos.show', $culto)->with('success', 'Asignaciones actualizadas correctamente.');
    }
}
