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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->decimal('montant_paye', 10, 2)->default(0);
            $table->unsignedTinyInteger('mois');
            $table->unsignedSmallInteger('annee');
            $table->date('date_paiement')->nullable();
            $table->enum('mode_paiement', ['especes', 'virement', 'mobile_money'])->default('especes');
            $table->enum('statut', ['paye', 'impaye', 'partiel'])->default('impaye');
            $table->string('reference')->nullable();
            $table->text('remarque')->nullable();
            $table->timestamps();

            $table->index(['athlete_id', 'mois', 'annee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
