<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reporte extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'id',
        'fecha',
        'hora_inicial',
        'hora_final',
        'tiempo_transcurrido',
        'actividad_id',
        'centrotrabajo_id',
        'ordentrabajo_id',

        'disponibilidad_id',
        'reproceso_id',

        'operario_id',

        // 'calendario_id',
        //19 sept2023
        'tipoFinalizacion', //BOUNDED 1: primera del dia | 2:intermedia | 3:Ultima del dia
        'tipoReporte', //acti, repro,disponibilidad

        //info de la orden de trabajo
        'nombreTablero',
        'OTItem',
        'TiempoEstimado',
    ];

    // public function reportes() { return $this->hasMany('App\Models\Reporte'); }

    public function actividad(): BelongsTo { return $this->BelongsTo(Actividad::class); }
    public function centrotrabajo(): BelongsTo { return $this->BelongsTo(Centrotrabajo::class,'centrotrabajo_id'); }
    // public function material(): BelongsTo { return $this->BelongsTo(Material::class, 'material_id'); }
    public function ordentrabajo(): BelongsTo { return $this->BelongsTo(Ordentrabajo::class); }
    public function operario(): BelongsTo { return $this->BelongsTo(User::class, 'operario_id'); }

    public function pieza(): BelongsTo { return $this->BelongsTo(Pieza::class); }

    public function disponibilidad(): BelongsTo { return $this->BelongsTo(Disponibilidad::class,'disponibilidad_id'); }
    public function reproceso(): BelongsTo { return $this->BelongsTo(Reproceso::class); }


    public function HorFinal($actualizar_reporte):void{
//            $horaFinal = Carbon::parse($this->hora_final);
        $horaFinal = Carbon::parse($actualizar_reporte['hora_final']);
        $horaInicial = Carbon::parse($this->hora_inicial);
        $tiemtras = number_format($horaFinal->diffInSeconds($horaInicial)/3600,3);
        $actualizar_reporte['tiempo_transcurrido'] = $tiemtras;
        $this->update($actualizar_reporte);
    }

}
