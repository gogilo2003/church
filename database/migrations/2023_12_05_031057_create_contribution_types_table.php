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
        Schema::create('contribution_types', function (Blueprint $table) {
            $table->id();
            $table->string('description')->unique();
            $table->boolean('recurrent')->default(false);
            $table->integer('recurrence_value')->nullable();
            $table->string('recurrence_unit')->nullable();
            $table->boolean('back_date')->default(false);
            $table->dateTime('deadline')->nullable();
            $table->decimal('amount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribution_types');
    }
};
