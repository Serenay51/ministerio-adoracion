<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PresidenteSeeder extends Seeder
{
    public function run()
    {
        // Verificamos si ya existe el presidente para evitar duplicados
        $email = 'marianela.salas@adoracion.com';

        if (User::where('email', $email)->doesntExist()) {
            User::create([
                'name' => 'Marianela Salas',
                'email' => $email,
                'password' => Hash::make('Maru123'), // Cambiá la contraseña que quieras
                'is_president' => true, // Suponiendo que tenés esta columna
            ]);
        }
    }
}
