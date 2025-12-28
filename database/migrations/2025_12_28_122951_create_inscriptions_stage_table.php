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
        Schema::create('inscriptions_stage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_formation_id')->constrained('stages_formation')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->enum('sexe', ['M', 'F'])->default('M');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('adresse')->nullable();
            $table->string('fonction')->nullable(); // Ex: Entraîneur, Assistant, etc.
            $table->string('structure')->nullable(); // Club ou organisation d'appartenance
            $table->string('niveau_etude')->nullable();
            $table->text('experience')->nullable(); // Expérience dans le domaine
            $table->foreignId('coach_id')->nullable()->constrained('coachs')->nullOnDelete(); // Si c'est un coach existant
            $table->enum('statut', ['inscrit', 'confirme', 'en_formation', 'diplome', 'echec', 'abandon'])->default('inscrit');
            $table->decimal('note_finale', 5, 2)->nullable();
            $table->text('appreciation')->nullable();
            $table->string('numero_certificat')->nullable()->unique(); // Numéro du certificat/diplôme
            $table->date('date_delivrance')->nullable(); // Date de délivrance du certificat
            $table->boolean('certificat_delivre')->default(false);
            $table->text('observations')->nullable();
            $table->timestamps();
            
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions_stage');
    }
};
