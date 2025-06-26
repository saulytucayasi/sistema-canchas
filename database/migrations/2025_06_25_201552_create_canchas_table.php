<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('canchas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['futbol', 'tenis', 'basquet', 'paddle', 'volley']);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_por_hora', 10, 2);
            $table->integer('capacidad');
            $table->time('hora_apertura');
            $table->time('hora_cierre');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canchas');
    }
};
