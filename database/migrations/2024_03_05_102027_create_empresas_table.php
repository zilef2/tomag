<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration{
    /** Run the migrations. @return void */
    public function up(){
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
			$table->string('nombre');
			$table->string('NIT');
            $table->timestamps();
        });

        Schema::table('actividad_centrotrabajo', function (Blueprint $table) {
            $table->dropForeign(['actividad_id']);
            $table->foreign('actividad_id')
                ->references('id')
                ->on('actividads')
                ->onDelete('cascade'); //restrict | set null
            $table->dropForeign(['centrotrabajo_id']);
            $table->foreign('centrotrabajo_id')
                ->references('id')
                ->on('actividads')
                ->onDelete('cascade'); //restrict | set null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresas');
    }
}//php artisan migrate --path=database\migrations\2024_03_05_102027_create_empresas_table.php

