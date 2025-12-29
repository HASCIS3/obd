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
        Schema::create('matchs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discipline_id')->constrained()->onDelete('cascade');
            $table->date('date_match');
            $table->time('heure_match')->nullable();
            $table->enum('type_match', ['domicile', 'exterieur'])->default('domicile');
            $table->string('adversaire'); // Ex: "Équipe Senou"
            $table->string('lieu')->nullable();
            $table->integer('score_obd')->nullable(); // Notre score
            $table->integer('score_adversaire')->nullable();
            $table->enum('resultat', ['victoire', 'defaite', 'nul', 'a_jouer'])->default('a_jouer');
            $table->enum('type_competition', ['championnat', 'coupe', 'tournoi', 'amical'])->default('amical');
            $table->string('nom_competition')->nullable(); // Ex: "Championnat régional 2025"
            $table->string('saison')->nullable(); // Ex: "2024-2025"
            $table->enum('phase', ['aller', 'retour', 'finale', 'demi_finale', 'quart_finale', 'poule', 'autre'])->nullable();
            $table->text('remarques')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matchs');
    }
};
