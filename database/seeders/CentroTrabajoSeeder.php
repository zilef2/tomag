<?php

namespace Database\Seeders;

use App\Models\Centrotrabajo;
use Illuminate\Database\Seeder;

class CentroTrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $palabraClave = 'Centro de Trabajo';
        // $limite = intval(env('seedersNumber'));
        $tomagExcelInfo = [
            'CORTE', //1
            'DOBLEZ', //2
            'SOLDADURA', //3
            'PULIDA', //4
            'ENSAMBLE', //5
            'COBRE', //6
            'CABLEADO', //7
            'INGENIERIA MECANICA', //8
            'INGENIERIA ELECTRICA', //9
            'MECATRONICA'
        ];

        foreach ($tomagExcelInfo as $key => $value) {
            Centrotrabajo::create([
                'nombre' => $value,
                'codigo' => $key+1
            ]);
        }
    }
}
