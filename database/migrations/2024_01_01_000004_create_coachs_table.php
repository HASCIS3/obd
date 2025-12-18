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
        Schema::create('coachs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('telephone')->nullable();
            $table->text('adresse')->nullable();
            $table->string('specialite')->nullable();
            $table->date('date_embauche')->nullable(); 
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Table pivot coach_discipline
        Schema::create('coach_discipline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('coachs')->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['coach_id', 'discipline_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coach_discipline');
        Schema::dropIfExists('coachs');
    }
};
