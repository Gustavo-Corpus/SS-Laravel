<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';
    protected $primaryKey = 'id_departamento';

    protected $fillable = ['nombre_departamento'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_departamento');
    }
}
