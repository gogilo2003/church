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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_type_id');
            $table->foreignId('member_id');
            $table->dateTime('contribution_date')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('contribution_status')->default('pending');
            $table->timestamps();
            $table->foreign('contribution_type_id')->references('id')->on('contribution_types')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
