<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // date_reservation devient la date de début ; date_fin permet les réservations multi-jours.
            // Nullable : une réservation d'un seul jour laisse date_fin à null (= égale à date_reservation).
            $table->date('date_fin')->nullable()->after('date_reservation');
        });

        // Rétro-compatibilité : les réservations existantes deviennent des réservations d'un jour.
        DB::table('reservations')->whereNull('date_fin')->update([
            'date_fin' => DB::raw('date_reservation'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('date_fin');
        });
    }
};
