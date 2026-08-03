<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hierarchy_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hierarchy_definition_id')->constrained('hierarchy_definitions')->cascadeOnDelete();
            $table->unsignedInteger('depth');
            $table->string('name');
            $table->string('plural_name');
            $table->boolean('allow_institutions')->default(true);
            $table->timestamps();

            $table->unique(['hierarchy_definition_id', 'depth']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hierarchy_levels');
    }
};
