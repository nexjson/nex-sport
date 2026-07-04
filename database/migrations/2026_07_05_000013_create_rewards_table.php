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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_games_id')->constrained('event_games')->onDelete('cascade');
            $table->string('reward_type'); // Cast as enum in model: CUP_DIGITAL, PRIZE, VOUCHER
            $table->integer('tier')->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('prize_amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
