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
        Schema::table('performances', function (Blueprint $table) {
            // Type de contexte : entrainement, match, competition, test_physique
            $table->enum('contexte', ['entrainement', 'match', 'competition', 'test_physique'])
                ->default('entrainement')
                ->after('type_evaluation');
            
            // Résultat pour les matchs : victoire, defaite, nul
            $table->enum('resultat_match', ['victoire', 'defaite', 'nul'])->nullable()->after('contexte');
            
            // Points/Buts marqués
            $table->integer('points_marques')->nullable()->after('resultat_match');
            
            // Points/Buts encaissés
            $table->integer('points_encaisses')->nullable()->after('points_marques');
            
            // Médaille obtenue : or, argent, bronze
            $table->enum('medaille', ['or', 'argent', 'bronze'])->nullable()->after('classement');
            
            // Note de condition physique (1-10)
            $table->tinyInteger('note_physique')->nullable()->after('medaille');
            
            // Note technique (1-10)
            $table->tinyInteger('note_technique')->nullable()->after('note_physique');
            
            // Note comportement/discipline (1-10)
            $table->tinyInteger('note_comportement')->nullable()->after('note_technique');
            
            // Note globale calculée (1-10)
            $table->decimal('note_globale', 3, 1)->nullable()->after('note_comportement');
            
            // Adversaire (pour les matchs)
            $table->string('adversaire')->nullable()->after('competition');
            
            // Lieu de l'événement
            $table->string('lieu')->nullable()->after('adversaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performances', function (Blueprint $table) {
            $table->dropColumn([
                'contexte',
                'resultat_match',
                'points_marques',
                'points_encaisses',
                'medaille',
                'note_physique',
                'note_technique',
                'note_comportement',
                'note_globale',
                'adversaire',
                'lieu',
            ]);
        });
    }
};
