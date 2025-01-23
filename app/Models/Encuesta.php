<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    public function up()
{
    Schema::create('encuestas', function (Blueprint $table) {
        $table->id();
        $table->integer('pregunta1');
        $table->integer('pregunta2');
        $table->text('pregunta3');
        $table->timestamps();
    });
}


class Encuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'pregunta1',
        'pregunta2',
        'pregunta3',
    ];
}




}
