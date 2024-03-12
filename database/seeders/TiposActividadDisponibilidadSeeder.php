<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\CentroCosto;
use App\Models\CentroTrabajo;
use App\Models\Disponibilidad;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TiposActividadDisponibilidadSeeder extends Seeder
{
    public function run() {
        $actividades = Actividad::all();
        foreach ($actividades as $actividade) {
            $actividade->update([
                'tipo' => 'TVA',
            ]);

        }
        Actividad::find(10)->update([
            'tipo' => 'TNVA',
        ]);

        $disponibilidades = Disponibilidad::all();
        foreach ($disponibilidades as $disponibilidade) {
            $disponibilidade->update([
                'tipo' => 'Programado',
            ]);
        }

        Disponibilidad::WhereIn('id',[1, 2, 4, 5, 6, 8, 9, 14])->update([
            'tipo' => 'No programado',
        ]);
    }
}
//php artisan db:seed --class=TiposActividadDisponibilidadSeeder
