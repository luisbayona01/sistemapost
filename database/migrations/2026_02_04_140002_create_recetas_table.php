<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Pivot/Link table for BOM (Bill of Materials)
        // Linking Products (Venta) to Insumos (Inventory)
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained()->cascadeOnDelete();

            // Quantity of insumo required for 1 unit of product
            $table->decimal('cantidad', 12, 3);

            // Optional: Override unit if needed, otherwise assumes insumo unit
            $table->string('unidad_medida')->nullable();

            $table->timestamps();

            // Prevent duplicates
            $table->unique(['producto_id', 'insumo_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recetas');
    }
};
