<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Llama al seeder del presidente
        $this->call([
            PresidenteSeeder::class,
            // Otros seeders que quieras agregar
        ]);
    }
}
