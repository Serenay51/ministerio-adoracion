<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Culto;
use App\Models\RolCulto;
use App\Models\User;
use App\Models\Cancion;
use App\Models\TonalidadPropuesta;
use Illuminate\Container\Attributes\Auth;

class CultoController extends Controller
{
    public function show(Culto $culto)
    {
        $culto->load(['rolCultos.user', 'canciones']);

        $cancionesDisponibles = Cancion::orderBy('titulo')->get();

        return view('cultos.show', compact('culto', 'cancionesDisponibles'));
    }


    public function formAsignar(Culto $culto)
    {
        // Obtener todos los usuarios
        $miembros = User::orderBy('name')->get();

        // Roles fijos
        $roles = ['director' => 'Director', 'musico' => 'Músico', 'coro_apoyo' => 'Coro de Apoyo'];

        // Asignaciones actuales
        $asignaciones = $culto->rolCultos()->get();

        return view('admin.cultos.asignar.controller', compact('culto', 'miembros', 'roles', 'asignaciones'));
    }

    public function guardarAsignacion(Request $request, Culto $culto)
    {
        $data = $request->validate([
            'asignaciones' => 'required|array',
            'asignaciones.*' => 'array',
            'asignaciones.*.*' => 'in:director,musico,coro_apoyo',
        ]);

        // Eliminar asignaciones anteriores
        $culto->rolCultos()->delete();

        foreach ($data['asignaciones'] as $userId => $roles) {
            foreach ($roles as $rol) {
                RolCulto::create([
                    'user_id' => $userId,
                    'culto_id' => $culto->id,
                    'rol' => $rol,
                ]);
            }
        }

        return redirect()->route('admin.cultos.asignar', $culto)->with('success', 'Asignaciones guardadas correctamente.');
    }
}
