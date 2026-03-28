<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'citas';
    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'id_usuario',
        'id_medico',
        'fecha',
        'hora',
        'estado',
        'motivo_cancelacion',
        'enlace_meet',
        'mensaje_medico',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
