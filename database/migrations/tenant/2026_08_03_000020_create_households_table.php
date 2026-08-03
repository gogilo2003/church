<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->foreignId('org_unit_id')->nullable()->constrained('organizational_units')->nullOnDelete();
            $table->string('name');
            $table->string('primary_contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->date('marriage_date')->nullable();
            $table->timestamps();
        });

        Schema::create('household_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('relationship')->default('other'); // head, spouse, child, parent, guardian, other
            $table->timestamps();

            $table->unique(['household_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('household_member');
        Schema::dropIfExists('households');
    }
};
