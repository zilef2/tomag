<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sexos = ['Masculino', 'Femenino'];
        $genPa = env('sap_gen');
        $nombreAdmin = 'Admin';
        $App = env('APP_NAME');
        $empresa = Empresa::create([
            'nombre' => 'Arcoli',
            'NIT'   => '123456'
        ]);

        $superadmin = User::create([
            'name'              => 'Superadmin',
            'email'             => 'ajelof2+8@gmail.com',
            'password'          => bcrypt($genPa.'super0.+-*'.$genPa),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[random_int(0, 1)],
            'identificacion' => '11232454',
            'celular' => '11232454',
            'empresa_id' => 1,

        ]);
        $superadmin->assignRole('superadmin');


        $admin = User::create([
            'name'              => "$nombreAdmin $App",
            'email'             => "alejofg2+8@gmail.com",
            'password'          => bcrypt($genPa.'0.+-*'.$genPa),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[random_int(0, 1)],
            'identificacion' => '11232411',
            'celular' => '11232454',
            'empresa_id' => 1,

        ]);
        $admin->assignRole('admin');

        $trabajador = User::create([
            'name'              => 'trabajador',
            'email'             => 'trabajador@trabajador.com',
            'password'          => bcrypt('trabajador00+*'),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[random_int(0, 1)],
            'identificacion' => '11232412',
            'celular' => '11232454',
            'empresa_id' => 1,
        ]);
        $trabajador->assignRole('trabajador');

        $nombresGenericos = [
            'jose' => '1152888999',
        ];

        $genero = 'Masculino';
        // $genero = 'Femenino';
        foreach ($nombresGenericos as $key => $value) {
            $yearRandom = (random_int(15, 39));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name'              => $key,
                'email'             => $key . '@' . $key . '.com',
                'password'          => bcrypt($genPa.'asd+-*'),//
                'email_verified_at' => date('Y-m-d H:i'),
                'identificacion' => $value,
                'celular' => '123456',
                'fecha_nacimiento' => $anios,
                'sexo' => $genero,
                'empresa_id' => 1,
            ]);

            if($genero)
                $unUsuario->assignRole('trabajador');
            else
                $unUsuario->assignRole('supervisor');
            $genero++;
        }

    // 'CORTE', //1
    // 'DOBLEZ', //2
    // 'SOLDADURA', //3
    // 'PULIDA', //4
    // 'ENSAMBLE', //5
    // 'COBRE', //6
    // 'CABLEADO', //7
    // 'INGENIERIA MECANICA', //8
    // 'INGENIERIA ELECTRICA' //9

        $tempuser = User::create(['sexo' => 'Masculino','password'=>bcrypt('Emerson Giraldo+*'),
            'area' => 'INGENIERIA', 'cargo'=> 'Pruebas de sofware','name' => 'Emerson Giraldo',
            'identificacion'=> '1235665', 'email'=> 'emerson.giraldo@consult-ing.com.co',
            'celular' => '3002738152', 'centrotrabajo_id' => 1,
            'empresa_id' => 1,

        ]); $tempuser->assignRole('admin');

        $tempuser = User::create(['sexo' => 'Masculino','password'=>bcrypt('jose499'.'*?'),
            'area' => 'INGENIERIA', 'cargo'=> 'Diseñador de Pruebas',
            'name' => 'Jose Felizzola', 'identificacion'=> '1066749869',
            'email'=> 'jennyjefff@outllok.com', 'celular' => '3511299070', 'centrotrabajo_id' => 1,
            'empresa_id' => 1,
            ]);
        $tempuser->assignRole('supervisor');
    }
}
/*
php artisan migrate --path=/database/migrations/nombre_de_la_migracion.php
php artisan db:seed --class=NombreDeLaSeeder
*/
