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
        Schema::create('suivis_scolaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->string('etablissement')->nullable();
            $table->string('classe')->nullable();
            $table->string('annee_scolaire')->nullable();
            $table->decimal('moyenne_generale', 5, 2)->nullable();
            $table->unsignedInteger('rang')->nullable();
            $table->text('observations')->nullable();
            $table->string('bulletin_path')->nullable();
            $table->timestamps();

            $table->index(['athlete_id', 'annee_scolaire']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suivis_scolaires');
    }
};
