<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('canal')->default('POS')->after('metodo_pago'); // POS, WEB, APP
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('canal');
        });
    }
};
