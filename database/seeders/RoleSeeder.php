<?php

namespace Database\Seeders;

use App\helpers\Myhelp;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $superadmin = Role::create(['name' => 'superadmin']);
        $admin = Role::create(['name' => 'admin']);

       $constantes = Myhelp::CargosYModelos();

        //ex: istrabajador
        foreach ($constantes['nombresDeCargos'] as $value) {
            $$value = Role::create(['name' => $value]);
            $$value->givePermissionTo(['is' . $value]);
        }

        $crudCompleto = ['delete', 'update', 'read', 'create'];
        foreach ($constantes['Models'] as $model) {
            foreach ($crudCompleto as $crud) {
                $superadmin->givePermissionTo([$crud . ' ' . $model]);
                $admin->givePermissionTo([$crud . ' ' . $model]);
            }
        }
        $isSomeThing = array_merge(  [ "isSuper", "isAdmin" ], $constantes['isSome']);
        $superadmin->givePermissionTo($isSomeThing);
        unset($isSomeThing[0]);
        $admin->givePermissionTo($isSomeThing);



        //otros
        $trabajador->givePermissionTo([
            'read reporte',  'create reporte',  // 'delete reporte',
        ]);
        $supervisor->givePermissionTo([
            'read reporte',  'create reporte', 'delete reporte', 'update reporte',
            'read user',  //'create user', 'update reporte',
        ]);
        $Jefe->givePermissionTo([
            'read reporte',  'create reporte', 'delete reporte', 'update reporte',
            'read user',  'create user', 'delete user', 'update user',
            'read ordentrabajo',  'create ordentrabajo', 'delete ordentrabajo', 'update ordentrabajo',
            'read actividad',  'create actividad', 'delete actividad', 'update actividad',
            'read centrotrabajo',  'create centrotrabajo', 'delete centrotrabajo', 'update centrotrabajo',
            'read disponibilidad',  'create disponibilidad', 'delete disponibilidad', 'update disponibilidad',
            'read reproceso',  'create reproceso', 'delete reproceso', 'update reproceso',
        ]);

        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
