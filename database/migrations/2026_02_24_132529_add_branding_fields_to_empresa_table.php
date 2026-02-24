<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('propietario');
            $table->string('primary_color')->default('#000000')->after('logo');
            $table->string('secondary_color')->default('#ffffff')->after('primary_color');
            $table->text('custom_css')->nullable()->after('secondary_color');
        });
    }

    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn(['logo', 'primary_color', 'secondary_color', 'custom_css']);
        });
    }
};
