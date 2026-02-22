<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->decimal('merma_esperada', 5, 2)->default(0)->after('cantidad'); // Porcentaje 0-100
        });
    }

    public function down()
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropColumn('merma_esperada');
        });
    }
};
