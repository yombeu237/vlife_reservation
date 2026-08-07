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
        Schema::create('options_reservation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compartiment_id')
                ->constrained('compartiments')
                ->restrictOnDelete();
            $table->string('libelle');
            $table->unsignedInteger('tarif')->default(0);
            $table->text('regle_description')->nullable();
            $table->unsignedSmallInteger('capacite');
            $table->string('type_calcul', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options_reservation');
    }
};
