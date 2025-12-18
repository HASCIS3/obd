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
        // Modifier l'enum pour ajouter les catégories de dobok
        DB::statement("ALTER TABLE paiements MODIFY COLUMN type_equipement ENUM('maillot', 'dobok', 'dobok_enfant', 'dobok_junior', 'dobok_senior') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE paiements MODIFY COLUMN type_equipement ENUM('maillot', 'dobok') NULL");
    }
};
