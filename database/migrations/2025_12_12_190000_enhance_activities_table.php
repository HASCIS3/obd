<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('type', ['competition', 'tournoi', 'match', 'entrainement', 'evenement', 'galerie'])->default('evenement')->after('id');
            $table->string('image')->nullable()->after('lieu');
            $table->string('video_url')->nullable()->after('image');
            $table->foreignId('discipline_id')->nullable()->after('video_url')->constrained('disciplines')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
            $table->dropColumn(['type', 'image', 'video_url', 'discipline_id']);
        });
    }
};
