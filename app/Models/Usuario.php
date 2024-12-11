<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios'; // Nombre de tu tabla existente
    protected $primaryKey = 'id_usuarios'; // Tu llave primaria

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'edad',
        'sexo',
        'estatus',
        'correo',
        'ocupacion',
        'avatar',
        'id_departamento',
        'id_estado'
    ];

    // Relaciones con otras tablas
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }
}
