<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_heads', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'asset', 'liability']);
            $table->string('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('vote_heads')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('offering_types') && ! Schema::hasColumn('offering_types', 'vote_head_id')) {
            Schema::table('offering_types', function (Blueprint $table) {
                $table->foreignId('vote_head_id')->nullable()->after('is_active')->constrained('vote_heads')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('offering_types') && Schema::hasColumn('offering_types', 'vote_head_id')) {
            Schema::table('offering_types', function (Blueprint $table) {
                $table->dropForeign(['vote_head_id']);
                $table->dropColumn('vote_head_id');
            });
        }

        Schema::dropIfExists('vote_heads');
    }
};
