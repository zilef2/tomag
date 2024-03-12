<?php

namespace Database\Seeders;

use App\Models\Ordentrabajo;
use Illuminate\Database\Seeder;

class OrdentrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $palabraClave = 'Orden de Trabajo ejemplo';
         $limite = (int)(env('seedersNumber'));
         for ($i = 0; $i < $limite; $i++) {
             Ordentrabajo::create([
                 'nombre' => $palabraClave . random_int(100, 10000),
                 'codigo' => random_int(100, 10000)
             ]);
         }
    }
}
