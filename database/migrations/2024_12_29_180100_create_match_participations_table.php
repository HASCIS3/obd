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
        Schema::create('match_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matchs')->onDelete('cascade');
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->boolean('titulaire')->default(false);
            $table->integer('minutes_jouees')->nullable();
            $table->integer('points_marques')->nullable(); // Basketball, Volleyball
            $table->integer('passes_decisives')->nullable();
            $table->integer('rebonds')->nullable(); // Basketball
            $table->integer('interceptions')->nullable();
            $table->integer('fautes')->nullable();
            $table->integer('cartons_jaunes')->nullable(); // Football
            $table->integer('cartons_rouges')->nullable();
            $table->decimal('note_performance', 3, 1)->nullable(); // Note sur 10
            $table->text('remarques')->nullable();
            $table->timestamps();

            // Un athlète ne peut participer qu'une fois à un match
            $table->unique(['match_id', 'athlete_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_participations');
    }
};
