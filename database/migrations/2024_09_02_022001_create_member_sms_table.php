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
        Schema::create('member_sms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id');
            $table->foreignId('sms_id');
            $table->string('messageId')->nullable();
            $table->string('status')->default('pending');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('sms_id')->references('id')->on('sms')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_sms');
    }
};
