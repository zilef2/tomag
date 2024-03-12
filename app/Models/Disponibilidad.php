<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Disponibilidad extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nombre',
        'tipo',
    ];

    public function centroTrabajos(): BelongsToMany { return $this->BelongsToMany(Centrotrabajo::class); }

}
