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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id');
            $table->string('receipt_number')->nullable();
            $table->mediumText('details')->nullable();
            $table->string('mode')->default('cash');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
            $table->foreign('contribution_id')->references('id')->on('contributions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
