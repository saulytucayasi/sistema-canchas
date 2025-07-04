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
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('voucher_pago')->nullable()->after('observaciones');
            $table->enum('estado_voucher', ['pendiente', 'verificado', 'rechazado'])->default('pendiente')->after('voucher_pago');
            $table->text('comentario_voucher')->nullable()->after('estado_voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn(['voucher_pago', 'estado_voucher', 'comentario_voucher']);
        });
    }
};
