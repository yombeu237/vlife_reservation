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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();
            $table->foreignId('utilisateur_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('option_id')
                ->constrained('options_reservation')
                ->restrictOnDelete();

            $table->date('date_reservation');
            $table->string('type_creneau', 20);
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();

            $table->unsignedSmallInteger('nombre_personnes')->default(1);
            $table->unsignedInteger('montant')->default(0);

            $table->string('statut', 20)->default('liste_attente');
            $table->boolean('modifie_manuellement')->default(false);

            $table->string('chemin_document_justificatif', 500)->nullable();
            $table->timestamp('date_validation_preuve')->nullable();

            $table->timestamps();

            $table->index(['date_reservation', 'statut']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
