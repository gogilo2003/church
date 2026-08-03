<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('household_id')->nullable()->after('org_unit_id')->constrained('households')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('household_id')->constrained('users')->nullOnDelete();
            $table->string('member_number')->nullable()->after('user_id')->index();
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('marital_status')->default('single')->after('gender');
            $table->string('occupation')->nullable()->after('marital_status');
            $table->string('national_id')->nullable()->after('occupation');
            $table->string('status')->default('active')->after('national_id')->index();
            $table->json('spiritual_milestones')->nullable()->after('status');
            $table->date('date_joined')->nullable()->after('spiritual_milestones');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['household_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'household_id',
                'user_id',
                'member_number',
                'middle_name',
                'marital_status',
                'occupation',
                'national_id',
                'status',
                'spiritual_milestones',
                'date_joined',
            ]);
        });
    }
};
