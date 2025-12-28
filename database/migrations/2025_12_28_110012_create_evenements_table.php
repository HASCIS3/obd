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
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);
            $table->text('description')->nullable();
            $table->enum('type', ['entrainement', 'competition', 'reunion', 'stage', 'autre'])->default('entrainement');
            $table->foreignId('discipline_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->string('lieu', 150)->nullable();
            $table->string('couleur', 7)->default('#14532d');
            $table->boolean('toute_journee')->default(false);
            $table->boolean('recurrent')->default(false);
            $table->string('recurrence_type')->nullable(); // daily, weekly, monthly
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('date_debut');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
