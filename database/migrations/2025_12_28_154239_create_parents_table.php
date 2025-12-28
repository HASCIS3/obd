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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('telephone')->nullable();
            $table->string('telephone_secondaire')->nullable();
            $table->string('adresse')->nullable();
            $table->string('profession')->nullable();
            $table->enum('lien_parente', ['pere', 'mere', 'tuteur', 'autre'])->default('pere');
            $table->text('notes')->nullable();
            $table->boolean('recevoir_notifications')->default(true);
            $table->boolean('recevoir_sms')->default(true);
            $table->boolean('actif')->default(true);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('telephone');
        });

        // Table pivot pour lier les parents aux athlètes (enfants)
        Schema::create('athlete_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->constrained()->onDelete('cascade');
            $table->enum('lien', ['pere', 'mere', 'tuteur', 'autre'])->default('tuteur');
            $table->boolean('contact_principal')->default(false);
            $table->boolean('autorise_recuperation')->default(true);
            $table->timestamps();
            
            $table->unique(['athlete_id', 'parent_id']);
            $table->index('athlete_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athlete_parent');
        Schema::dropIfExists('parents');
    }
};
