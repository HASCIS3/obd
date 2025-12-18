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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained()->onDelete('cascade');
            $table->date('date_evaluation');
            $table->string('type_evaluation')->nullable();
            $table->decimal('score', 10, 2)->nullable();
            $table->string('unite')->nullable();
            $table->text('observations')->nullable();
            $table->string('competition')->nullable();
            $table->unsignedInteger('classement')->nullable();
            $table->timestamps();

            $table->index(['athlete_id', 'discipline_id', 'date_evaluation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
