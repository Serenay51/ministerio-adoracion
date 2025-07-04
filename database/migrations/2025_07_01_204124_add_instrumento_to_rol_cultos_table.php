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
        Schema::table('rol_cultos', function (Blueprint $table) {
            $table->string('instrumento')->nullable()->after('rol');
        });
    }

    public function down(): void
    {
        Schema::table('rol_cultos', function (Blueprint $table) {
            $table->dropColumn('instrumento');
        });
    }
};
