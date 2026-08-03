<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'members',
            'attendances',
            'tithes',
            'offerings',
            'contributions',
            'departments',
            'groups',
            'sms',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'org_unit_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('org_unit_id')->nullable()->constrained('organizational_units')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'members',
            'attendances',
            'tithes',
            'offerings',
            'contributions',
            'departments',
            'groups',
            'sms',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'org_unit_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['org_unit_id']);
                    $table->dropColumn('org_unit_id');
                });
            }
        }
    }
};
