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
        Schema::create('stages_formation', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('code')->unique(); // Code unique du stage (ex: FF-2025-001)
            $table->text('description')->nullable();
            $table->enum('type', ['formation_formateurs', 'recyclage', 'specialisation', 'initiation', 'perfectionnement'])->default('formation_formateurs');
            $table->foreignId('discipline_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('lieu');
            $table->string('organisme')->default('INJS'); // Institut National de la Jeunesse et des Sports
            $table->text('programme')->nullable(); // Contenu du programme
            $table->integer('duree_heures')->nullable(); // Durée totale en heures
            $table->integer('places_disponibles')->default(30);
            $table->decimal('frais_inscription', 10, 2)->default(0);
            $table->enum('type_certification', ['diplome', 'certificat', 'attestation'])->default('certificat');
            $table->string('intitule_certification')->nullable(); // Ex: "Certificat de Formation des Formateurs"
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->text('conditions_admission')->nullable();
            $table->text('objectifs')->nullable();
            $table->json('encadreurs')->nullable(); // Liste des encadreurs/formateurs
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['date_debut', 'date_fin']);
            $table->index('statut');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages_formation');
    }
};
