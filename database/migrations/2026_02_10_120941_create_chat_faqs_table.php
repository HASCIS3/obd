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
        Schema::create('chat_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general');
            $table->json('keywords');
            $table->text('question');
            $table->text('answer');
            $table->integer('priority')->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_faqs');
    }
};
