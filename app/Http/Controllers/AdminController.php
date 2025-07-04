<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Culto;
use App\Models\Cancion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{

    public function index()
    {
        return view('admin.index'); // O donde quieras que vaya la vista principal de admin
    }
    // Listar usuarios
    public function usuarios()
    {
        // Traemos todos los usuarios que NO son presidente
        $usuarios = User::where('is_president', false)->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    // Crear usuario
    public function crearUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_president' => false,
        ]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario creado correctamente.');
    }

    // Actualizar usuario
    public function actualizarUsuario(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario
    public function eliminarUsuario(User $user)
    {
        $user->delete();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario eliminado correctamente.');
    }

        // Listar cultos
    public function cultos()
    {
        $cultos = Culto::orderBy('fecha', 'desc')->get();
        return view('admin.cultos.index', compact('cultos'));
    }

    // Crear culto
    public function crearCulto(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|unique:cultos,fecha',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Culto::create([
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'created_by' => Auth::id(),  // ¡ACA NO PUEDE FALTAR!
        ]);

        return redirect()->route('admin.cultos')->with('success', 'Culto creado correctamente.');
    }

    // Actualizar culto
    public function actualizarCulto(Request $request, Culto $culto)
    {
        $request->validate([
            'fecha' => 'required|date|unique:cultos,fecha,' . $culto->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $culto->update($request->only('fecha', 'descripcion'));

        return redirect()->route('admin.cultos')->with('success', 'Culto actualizado correctamente.');
    }

    // Eliminar culto
    public function eliminarCulto(Culto $culto)
    {
        $culto->delete();

        return redirect()->route('admin.cultos')->with('success', 'Culto eliminado correctamente.');
    }

    // Mostrar listado de canciones
    public function canciones()
    {
        $canciones = Cancion::orderBy('titulo')->get();
        return view('admin.canciones.index', compact('canciones'));
    }

    // Crear una nueva canción
    public function crearCancion(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'nullable|string|max:255',
            'letra' => 'nullable|string',
        ]);

        Cancion::create([
            'titulo' => $request->titulo,
            'autor' => $request->autor,
            'letra' => $request->letra,
            'created_by' => Auth::id(),  // <-- acá asignás el id del usuario logueado
        ]);

        return redirect()->route('admin.canciones')->with('success', 'Canción creada correctamente.');
    }

    // Actualizar canción existente
    public function actualizarCancion(Request $request, Cancion $cancion)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'nullable|string|max:255',
            'letra' => 'nullable|string',
        ]);

        $cancion->update($request->only('titulo', 'autor', 'letra'));

        return redirect()->route('admin.canciones')->with('success', 'Canción actualizada correctamente.');
    }

    // Eliminar canción
    public function eliminarCancion(Cancion $cancion)
    {
        $cancion->delete();

        return redirect()->route('admin.canciones')->with('success', 'Canción eliminada con éxito.');
    }
}
