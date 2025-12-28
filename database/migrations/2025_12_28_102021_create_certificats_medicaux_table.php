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
        Schema::create('certificats_medicaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['aptitude', 'inaptitude_temporaire', 'inaptitude_definitive', 'suivi'])->default('aptitude');
            $table->date('date_examen');
            $table->date('date_expiration');
            $table->string('medecin', 100);
            $table->string('etablissement', 150)->nullable();
            $table->enum('statut', ['valide', 'expire', 'en_attente'])->default('valide');
            $table->boolean('apte_competition')->default(true);
            $table->boolean('apte_entrainement')->default(true);
            $table->text('restrictions')->nullable();
            $table->text('observations')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
            
            $table->index('athlete_id');
            $table->index('statut');
            $table->index('date_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificats_medicaux');
    }
};
