<?php

namespace App\helpers;

use Carbon\Carbon;
use Carbon\Translator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MyModels {


    //<editor-fold desc="zona estatica y permisos">

    /*
Tienda
Producto
Inventario
Venta_detalle
Venta
Compra
Mascota
Movimiento_financiero
Gasto
Tipo_venta
Tipo_gasto
Categoria_producto

     */
    public static function namesOfCargos(){
        return [
            'trabajador',//1
            'supervisor',//2
        ];
    }

    public static function WebModels(){
        return array_merge(self::WebVueModels(),[
            'permission',
            'user',
            'role',
        ]);
    }
    public static function WebVueModels(){
        return [
            'producto',
            'inventario',
            'cliente',


        ];
    }
    public static function WebVueModels_side(){
        return [
            'producto',
            'inventario',
            'cliente',

            'venta',
            'compra',
            'mascota',
            'movimiento_financiero',
            'gasto',
            'tipo_venta',
            'tipo_gasto',
            'categoria_producto',
        ];
    }
    public static function CrudCompletou()
    {
        return [
            'delete',
            'update',
            'read',
            'create',
            'specifyUpdate'
        ];
    }
    //</editor-fold>

    public static function CargosYModelos() {
        $nombresDeCargos = self::namesOfCargos();
        $isSome = [];
        foreach ($nombresDeCargos as $key => $value) {
            $isSome[] = 'is' . $value;
        }

        return [
            'nombresDeCargos' => $nombresDeCargos,
            'isSome' => $isSome,
        ];
    }

    public static function getPermissionToNumber($permissions) {
        $contador = 1;
        $nombresDeCargos = self::namesOfCargos();
        foreach($nombresDeCargos as $cargo){
            if($permissions === $cargo) return $contador;
            $contador++;
        }

        if($permissions === 'admin') return 9;
        if($permissions === 'superadmin') return 10;
        return 0;
    }
}
