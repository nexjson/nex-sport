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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('banner')->nullable();
            $table->foreignId('organizer_id')->constrained('organizers')->onDelete('cascade');
            $table->string('tournament_type'); // single_elimination, double_elimination, round_robin, swiss
            $table->string('location')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->timestamp('registration_start');
            $table->timestamp('registration_end');
            $table->string('status')->default('draft'); // draft, waiting_payment, waiting_verification, registration, ongoing, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
