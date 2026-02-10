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
            // MySQL supporte MODIFY COLUMN avec ENUM
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'coach', 'athlete', 'parent') DEFAULT 'athlete'");
        }
        // SQLite stocke les enums comme des strings, pas besoin de modifier la structure
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'coach', 'athlete') DEFAULT 'athlete'");
        }
    }
};
