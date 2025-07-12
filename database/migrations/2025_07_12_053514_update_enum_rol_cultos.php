<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rol_cultos', function (Blueprint $table) {
                DB::statement("ALTER TABLE rol_cultos DROP CONSTRAINT rol_cultos_rol_check");
                DB::statement("ALTER TABLE rol_cultos ADD CONSTRAINT rol_cultos_rol_check CHECK (rol IN ('director', 'musico', 'coro_apoyo', 'computadora', 'sonidista'))");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rol_cultos', function (Blueprint $table) {
            DB::statement("ALTER TABLE rol_cultos DROP CONSTRAINT rol_cultos_rol_check");
            DB::statement("ALTER TABLE rol_cultos ADD CONSTRAINT rol_cultos_rol_check CHECK (rol IN ('director', 'musico', 'coro_apoyo'))");
        });
    }
};
