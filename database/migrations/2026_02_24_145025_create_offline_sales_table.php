<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offline_sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('user_id')->constrained('users');
            $table->string('local_id')->nullable(); // ID interno del frontend
            $table->dateTime('fecha_local');
            $table->json('data_json'); // Payload completo de la venta
            $table->decimal('total', 15, 2);
            $table->string('estado')->default('PENDIENTE'); // PENDIENTE, SINCRONIZADA, ERROR
            $table->text('error_message')->nullable();
            $table->foreignId('venta_id')->nullable()->constrained('ventas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_sales');
    }
};
