<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offering_types', function (Blueprint $table) {
            if (! Schema::hasColumn('offering_types', 'description')) {
                $table->string('description')->nullable()->after('name');
            }
            if (! Schema::hasColumn('offering_types', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('offering_types', function (Blueprint $table) {
            $table->dropColumn(array_filter(['description', 'is_active'], fn ($col) => Schema::hasColumn('offering_types', $col)));
        });
    }
};
