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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_games_id')->constrained('event_games')->onDelete('cascade');
            $table->integer('round');
            $table->integer('match_order');
            $table->foreignId('squad_home_id')->nullable()->constrained('squads')->onDelete('set null');
            $table->foreignId('squad_away_id')->nullable()->constrained('squads')->onDelete('set null');
            $table->integer('score_home')->default(0);
            $table->integer('score_away')->default(0);
            $table->foreignId('winner_id')->nullable()->constrained('squads')->onDelete('set null');
            $table->string('status')->default('scheduled'); // scheduled, live, completed, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
