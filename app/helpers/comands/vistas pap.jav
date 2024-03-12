//misterdebug - crud-generator-laravel
(example-project: tomag)

    php artisan make:crud Parametro "prompEjercicios:string, NumeroTicketDefecto:integer"

    php artisan make:crud Reporte "nombre:string, fecha:string, disponibilidad:string, hora_inicial:datetime, hora_final:datetime, cantidad:integer, Tiempo:string"
        actividad_id
        centrotrabajo_id
        disponibilidad_id
        material_id //no necesary crud
        operario_id
        ordentrabajo_id //Como los pedidos: se dice lo q se tiene q hacer
        reproceso_id


php artisan make:crud Actividad "codigo:string"
php artisan make:crud Centrotrabajo "codigo:string"
php artisan make:crud Disponibilidad "codigo:string"
php artisan make:crud Material "codigo:string"
php artisan make:crud Ordentrabajo "codigo:string"
php artisan make:crud Pieza "codigo:string"
php artisan make:crud Reproceso "codigo:string"
//5marzo2024
php artisan make:crud Empresa "nombre:string,NIT:string"

//19 septiembre 2023

//? php artisan make:crud Calendario "anio:"



//just model
php artisan make:model Generico -all

//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
//its donest need a Model -> materia_user



//#Utilidades
para borrar:  php artisan rm:crud post --force
    php artisan rm:crud centroTrabajo --force
//#laravel excel
php artisan make:0import PersonalImport --model=User
php artisan make:0import PersonalUniversidadImport --model=User

php artisan make:export TodaBDExport

// node
vue-datapicker
// laravel
composer require maatwebsite/excel



// parte generica
