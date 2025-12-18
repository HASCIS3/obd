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
        Schema::table('paiements', function (Blueprint $table) {
            // Type de paiement : cotisation mensuelle, inscription, équipement
            $table->enum('type_paiement', ['cotisation', 'inscription', 'equipement'])
                ->default('cotisation')
                ->after('athlete_id');
            
            // Frais d'inscription (optionnel)
            $table->decimal('frais_inscription', 10, 2)->nullable()->after('type_paiement');
            
            // Type d'équipement : maillot (basket/volley) ou dobok (taekwondo)
            $table->enum('type_equipement', ['maillot', 'dobok'])->nullable()->after('frais_inscription');
            
            // Frais d'équipement
            $table->decimal('frais_equipement', 10, 2)->nullable()->after('type_equipement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropColumn(['type_paiement', 'frais_inscription', 'type_equipement', 'frais_equipement']);
        });
    }
};
