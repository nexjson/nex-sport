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
        Schema::create('reward_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade');
            $table->integer('amount');
            $table->foreignId('squad_id')->nullable()->constrained('squads')->onDelete('set null');
            $table->foreignId('player_id')->nullable()->constrained('players')->onDelete('set null');
            $table->foreignId('claimed_by_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, processing, paid, failed
            $table->string('payment_method')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('payment_receipt')->nullable();
            $table->timestamp('claimed_at')->useCurrent();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_claims');
    }
};
