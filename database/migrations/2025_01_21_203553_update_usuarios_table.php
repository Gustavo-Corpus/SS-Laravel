<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsuariosTable extends Migration
{
    public function up()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Agregar campo de rol si no existe
            if (!Schema::hasColumn('usuarios', 'rol')) {
                $table->enum('rol', ['admin', 'empleado', 'cliente'])->default('cliente');
            }
            // Campos para métricas de tickets
            if (!Schema::hasColumn('usuarios', 'calificacion_promedio')) {
                $table->decimal('calificacion_promedio', 3, 2)->nullable();
            }
            if (!Schema::hasColumn('usuarios', 'tickets_resueltos')) {
                $table->integer('tickets_resueltos')->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['rol', 'calificacion_promedio', 'tickets_resueltos']);
        });
    }
}
