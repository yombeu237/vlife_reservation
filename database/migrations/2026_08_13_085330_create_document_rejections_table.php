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
        Schema::create('document_rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->string('type_attendu', 30)->nullable();
            $table->string('type_fourni', 30)->nullable();
            $table->string('code_raison', 40);          // type_incoherent | doublon_signature | doublon_fichier
            $table->string('raison', 500);
            $table->date('date_document')->nullable();
            $table->unsignedInteger('montant_document')->nullable();
            $table->string('numero_facture', 50)->nullable();
            $table->timestamps();

            $table->index(['utilisateur_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_rejections');
    }
};
