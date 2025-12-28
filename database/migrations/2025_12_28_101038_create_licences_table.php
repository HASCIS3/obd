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
        Schema::create('licences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained()->onDelete('cascade');
            $table->string('numero_licence', 50)->unique();
            $table->string('federation')->default('FMJSEP'); // Fédération Malienne de la Jeunesse, des Sports et de l'Éducation Physique
            $table->enum('type', ['nationale', 'regionale', 'locale'])->default('nationale');
            $table->enum('categorie', ['U11', 'U13', 'U15', 'U17', 'U19', 'U21', 'Senior', 'Veteran'])->nullable();
            $table->date('date_emission');
            $table->date('date_expiration');
            $table->enum('statut', ['active', 'expiree', 'suspendue', 'annulee'])->default('active');
            $table->string('saison')->nullable(); // Ex: 2024-2025
            $table->decimal('frais_licence', 10, 2)->default(0);
            $table->boolean('paye')->default(false);
            $table->string('document')->nullable(); // Scan de la licence
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['athlete_id', 'discipline_id']);
            $table->index('statut');
            $table->index('date_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licences');
    }
};
