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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('squad_id')->constrained('squads')->onDelete('cascade');
            $table->foreignId('event_games_id')->constrained('event_games')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('payment_status')->default('unpaid'); // free, unpaid, paid, refunded
            $table->integer('ticket_price')->default(0);
            $table->integer('admin_fee')->default(0);
            $table->integer('amount_paid')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_receipt')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_receipt')->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
