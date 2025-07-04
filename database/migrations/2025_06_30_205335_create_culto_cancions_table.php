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
        Schema::create('culto_cancions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('culto_id')->constrained()->onDelete('cascade');
            $table->foreignId('cancion_id')->constrained()->onDelete('cascade');
            $table->string('estructura')->nullable();
            $table->string('tonalidad')->nullable(); // la aprobada
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('culto_cancions');
    }
};
