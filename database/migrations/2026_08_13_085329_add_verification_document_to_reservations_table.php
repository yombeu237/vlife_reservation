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
        Schema::table('reservations', function (Blueprint $table) {
            // Type de document attendu/fourni : vtc_coworking | facture_sportbar
            $table->string('type_document', 30)->nullable()->after('date_validation_preuve');
            // Métadonnées saisies par l'employé d'après le document papier (fiable, contourne l'OCR manuscrit).
            $table->date('date_document')->nullable()->after('type_document');
            $table->unsignedInteger('montant_document')->nullable()->after('date_document');
            $table->string('numero_facture', 50)->nullable()->after('montant_document');
            // Empreinte SHA-256 du fichier téléversé (anti-renvoi exact).
            $table->string('hash_document', 64)->nullable()->after('numero_facture');

            $table->index(['type_document', 'date_document', 'montant_document', 'numero_facture'], 'idx_signature_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('idx_signature_document');
            $table->dropColumn(['type_document', 'date_document', 'montant_document', 'numero_facture', 'hash_document']);
        });
    }
};
