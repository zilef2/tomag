<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Run the migrations. */
    public function up(): void{
        Schema::table('reportes', function (Blueprint $table) {
            $table->string('codigo')->nullable()->change();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas')
                ->onDelete('restrict'); //cascade | set null
        });
        Schema::table('actividads', function (Blueprint $table) {
            $table->string('codigo')->nullable()->change();
            $table->unsignedBigInteger('empresa_id')->nullable();
                        $table->foreign('empresa_id')
                            ->references('id')
                            ->on('empresas')
                            ->onDelete('restrict'); //cascade | set null
        });
        Schema::table('centrotrabajos', function (Blueprint $table) {
            $table->string('codigo')->nullable()->change();
            $table->unsignedBigInteger('empresa_id')->nullable();
                        $table->foreign('empresa_id')
                            ->references('id')
                            ->on('empresas')
                            ->onDelete('restrict'); //cascade | set null
        });
        Schema::table('disponibilidads', function (Blueprint $table) {
            $table->string('codigo')->nullable()->change();
            $table->unsignedBigInteger('empresa_id')->nullable();
                        $table->foreign('empresa_id')
                            ->references('id')
                            ->on('empresas')
                            ->onDelete('restrict'); //cascade | set null
        });
        Schema::table('ordentrabajos', function (Blueprint $table) {
            $table->string('codigo')->nullable()->change();
            $table->unsignedBigInteger('empresa_id')->nullable();
                        $table->foreign('empresa_id')
                            ->references('id')
                            ->on('empresas')
                            ->onDelete('restrict'); //cascade | set null
        });
        Schema::table('reprocesos', function (Blueprint $table) {
            $table->string('codigo')->nullable()->change();
            $table->unsignedBigInteger('empresa_id')->nullable();
                        $table->foreign('empresa_id')
                            ->references('id')
                            ->on('empresas')
                            ->onDelete('restrict'); //cascade | set null
        });
//        Schema::table('users', function (Blueprint $table) {
//            $table->dropForeign('centrotrabajo_id');
//        });

        Schema::table('users', function (Blueprint $table) {
//            $table->dropColumn("centrotrabajo_id");
            $table->unsignedBigInteger('empresa_id')->default(1);
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas')
                ->onDelete('restrict'); //cascade | set null
        });
    }
    /** Reverse the migrations. */
    public function down(): void{}
};
/*
php artisan migrate --path=/database/migrations/2024_03_06_095012_codigos_nulos__y_empresaid.php
php artisan migrate --path=/database/migrations/nombre_de_la_migracion.php
php artisan db:seed --class=NombreDeLaSeeder
*/
