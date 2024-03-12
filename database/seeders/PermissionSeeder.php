<?php

namespace Database\Seeders;

use App\helpers\Myhelp;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'isSuper']);
        Permission::create(['name' => 'isAdmin']);

        $constantes = Myhelp::CargosYModelos();


        foreach ($constantes['nombresDeCargos'] as $key => $value) {
            Permission::create(['name' => 'is'.$value]);
        }

        $crudCompleto = ['delete', 'update', 'read', 'create', 'cambiarNombre'];
        foreach ($constantes['Models'] as $model) {
            foreach ($crudCompleto as $crud) {
                Permission::create(['name' => $crud . ' ' . $model]);
            }
        }
    }
}
