<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // En SQLite No se puede cambiar el enum fácilmente, pero en MySQL/Postgres sí.
        // Asumiendo MySQL/Postgres por el contexto (WAMP).
        // Agregamos 'oz' y 'lb' si es necesario.
        DB::statement("ALTER TABLE insumos MODIFY COLUMN unidad_medida ENUM('kg', 'g', 'l', 'ml', 'und', 'oz', 'lb') DEFAULT 'und'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE insumos MODIFY COLUMN unidad_medida ENUM('kg', 'g', 'l', 'ml', 'und') DEFAULT 'und'");
    }
};
