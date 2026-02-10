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
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL supporte MODIFY COLUMN
            DB::statement("ALTER TABLE paiements MODIFY COLUMN type_equipement ENUM('maillot', 'dobok', 'dobok_enfant', 'dobok_junior', 'dobok_senior') NULL");
        } else {
            // SQLite et autres : utiliser une approche compatible
            // SQLite stocke les enums comme des strings, pas besoin de modifier la structure
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE paiements MODIFY COLUMN type_equipement ENUM('maillot', 'dobok') NULL");
        }
    }
};
