<?php

namespace Database\Seeders;

use App\Models\Actividad;
use App\Models\Centrotrabajo;
use App\Models\Disponibilidad;
use App\Models\Empresa;
use App\Models\Ordentrabajo;
use App\Models\Reporte;
use App\Models\Reproceso;
use Illuminate\Database\Seeder;

class EmpresaLigadaSeeder extends Seeder{
    /** Run the database seeds. @return void */
    public function run(){
        $reportes = Reporte::Where('id','>',0)->update(['empresa_id' => 1]);
        $actividads = Actividad::Where('id','>',0)->update(['empresa_id' => 1]);
        $centrotrabajos = Centrotrabajo::Where('id','>',0)->update(['empresa_id' => 1]);
        $disponibilidads = Disponibilidad::Where('id','>',0)->update(['empresa_id' => 1]);
        $ordentrabajos = Ordentrabajo::Where('id','>',0)->update(['empresa_id' => 1]);
        $reprocesos = Reproceso::Where('id','>',0)->update(['empresa_id' => 1]);
    }
}
/*
php artisan db:seed --class=EmpresaLigadaSeeder.php
php artisan db:seed --class=EmpresaLigadaSeeder

*/
