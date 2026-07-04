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
        Schema::create('transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignId('from_squad_id')->nullable()->constrained('squads')->onDelete('set null');
            $table->foreignId('to_squad_id')->nullable()->constrained('squads')->onDelete('set null');
            $table->string('transfer_type'); // 'join', 'transfer', 'release', 'disband'
            $table->integer('transfer_fee')->nullable();
            $table->timestamp('transfer_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_histories');
    }
};
