<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
   protected $table = 'evaluaciones';
   protected $primaryKey = 'id_evaluacion';
   public $timestamps = false;

   protected $fillable = [
       'id_usuario',
       'mes',
       'anio',
       'calificacion',
       'comentarios'
   ];

   public function usuario()
   {
       return $this->belongsTo(Usuario::class, 'id_usuario');
   }
}
