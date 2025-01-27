<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTables extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('numero_ticket', 20)->unique();
            $table->integer('id_usuario');     // Usuario que crea el ticket
            $table->integer('id_asignado')->nullable();  // Empleado asignado
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->enum('estado', ['abierto', 'en_proceso', 'resuelto', 'cerrado'])->default('abierto');
            $table->timestamps();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('id_usuario')
                  ->references('id_usuarios')
                  ->on('usuarios')
                  ->onDelete('cascade');

            $table->foreign('id_asignado')
                  ->references('id_usuarios')
                  ->on('usuarios')
                  ->onDelete('set null');
        });

        Schema::create('calificaciones_tickets', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('id_ticket');
            $table->integer('id_usuario');    // Usuario que califica
            $table->integer('id_empleado');   // Empleado calificado
            $table->integer('calificacion');
            $table->text('comentario')->nullable();
            $table->timestamps();
        });

        Schema::table('calificaciones_tickets', function (Blueprint $table) {
            $table->foreign('id_ticket')
                  ->references('id')
                  ->on('tickets')
                  ->onDelete('cascade');

            $table->foreign('id_usuario')
                  ->references('id_usuarios')
                  ->on('usuarios')
                  ->onDelete('cascade');

            $table->foreign('id_empleado')
                  ->references('id_usuarios')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('calificaciones_tickets');
        Schema::dropIfExists('tickets');
    }
}
