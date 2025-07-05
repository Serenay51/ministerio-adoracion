<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CultoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\CultoCancionController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{year?}/{month?}', [DashboardController::class, 'index'])->name('dashboard.date');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('president')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

        // Usuarios
        Route::get('/admin/usuarios', [AdminController::class, 'usuarios'])->name('admin.usuarios');
        Route::post('/admin/usuarios', [AdminController::class, 'crearUsuario'])->name('admin.usuarios.crear');
        Route::put('/admin/usuarios/{user}', [AdminController::class, 'actualizarUsuario'])->name('admin.usuarios.actualizar');
        Route::delete('/admin/usuarios/{user}', [AdminController::class, 'eliminarUsuario'])->name('admin.usuarios.eliminar');

        // Canciones
        Route::get('/admin/canciones', [AdminController::class, 'canciones'])->name('admin.canciones');
        Route::post('/admin/canciones', [AdminController::class, 'crearCancion'])->name('admin.canciones.crear');
        Route::put('/admin/canciones/{cancion}', [AdminController::class, 'actualizarCancion'])->name('admin.canciones.actualizar');
        Route::delete('/admin/canciones/{cancion}', [AdminController::class, 'eliminarCancion'])->name('admin.canciones.eliminar');

        // Cultos
        Route::get('/admin/cultos', [AdminController::class, 'cultos'])->name('admin.cultos');
        Route::post('/admin/cultos', [AdminController::class, 'crearCulto'])->name('admin.cultos.crear');
        Route::put('/admin/cultos/{culto}', [AdminController::class, 'actualizarCulto'])->name('admin.cultos.actualizar');
        Route::delete('/admin/cultos/{culto}', [AdminController::class, 'eliminarCulto'])->name('admin.cultos.eliminar');
        Route::get('/admin/cultos/{culto}/asignar', [CultoController::class, 'formAsignar'])->name('admin.cultos.asignar.controller');

        Route::get('cultos/{culto}/asignar', [AsignacionController::class, 'edit'])->name('admin.cultos.asignar');
        Route::post('cultos/{culto}/asignar', [AsignacionController::class, 'update'])->name('admin.cultos.asignar.update');
    });
    Route::get('/cultos/{culto}', [CultoController::class, 'show'])->name('cultos.show');
    Route::post('cultos/{culto}/canciones/asignar', [CultoCancionController::class, 'store'])->name('cultos.canciones.asignar');
    Route::delete('/cultos/{culto}/canciones/{cancion}', [CultoCancionController::class, 'destroy'])->name('cultos.canciones.destroy');
    Route::post('/cultos/{culto}/canciones/reordenar', [CultoCancionController::class, 'reordenar'])->name('cultos.canciones.reordenar');
    Route::post('/cultos/{culto}/canciones/actualizar-estructura', [CultoCancionController::class, 'actualizarEstructura'])->name('cultos.canciones.actualizarEstructura');
    Route::post('/cultos/{culto}/canciones/actualizar-tonalidad', [CultoCancionController::class, 'actualizarTonalidad'])->name('cultos.canciones.actualizarTonalidad');



});

Route::get('/debug-env', function () {
    return env('APP_DEBUG') ? 'Debug ON' : 'Debug OFF';
});

require __DIR__.'/auth.php';
