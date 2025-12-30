<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combats_taekwondo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencontre_id')->constrained('matchs')->onDelete('cascade');
            
            // Combattant Rouge
            $table->foreignId('athlete_rouge_id')->nullable()->constrained('athletes')->onDelete('set null');
            $table->string('nom_rouge')->nullable();
            $table->string('club_rouge')->nullable();
            $table->string('categorie_rouge')->nullable();
            
            // Combattant Bleu
            $table->foreignId('athlete_bleu_id')->nullable()->constrained('athletes')->onDelete('set null');
            $table->string('nom_bleu')->nullable();
            $table->string('club_bleu')->nullable();
            $table->string('categorie_bleu')->nullable();
            
            // Scores par round (JSON pour flexibilité)
            $table->json('rounds')->nullable();
            
            // Scores totaux
            $table->integer('score_rouge')->default(0);
            $table->integer('score_bleu')->default(0);
            
            // Statut et résultat
            $table->enum('statut', ['a_jouer', 'en_cours', 'termine'])->default('a_jouer');
            $table->enum('vainqueur', ['rouge', 'bleu', 'nul', 'non_determine'])->default('non_determine');
            $table->enum('type_victoire', ['points', 'ecart_20', 'disqualification', 'abandon', 'ko', 'decision_arbitre'])->nullable();
            
            // Infos combat
            $table->integer('round_actuel')->default(1);
            $table->string('categorie_poids')->nullable();
            $table->string('categorie_age')->nullable();
            $table->text('remarques')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combats_taekwondo');
    }
};
