<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_sharing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            // Source Revenue Scope
            $table->enum('source_type', ['tithe', 'offering_type', 'project']);
            $table->unsignedBigInteger('source_id')->nullable(); // offering_type_id or project_id if specific item

            // Source Org Scope (where collection happened)
            $table->enum('source_scope_type', ['global', 'hierarchy_level', 'org_unit'])->default('global');
            $table->unsignedBigInteger('source_scope_id')->nullable();

            // Destination Unit
            $table->enum('destination_type', ['parent', 'ancestor_at_level', 'specific_unit'])->default('parent');
            $table->unsignedBigInteger('destination_id')->nullable();

            // Flow direction & Priority
            $table->enum('direction', ['upward', 'downward', 'direct'])->default('upward');
            $table->integer('priority')->default(0);

            // Calculation Logic
            $table->enum('calculation_type', ['percentage', 'fixed_amount'])->default('percentage');
            $table->decimal('value', 10, 4); // rate % or fixed $
            $table->decimal('min_collection_threshold', 12, 2)->default(0.00);
            $table->decimal('max_cap_amount', 12, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('revenue_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revenue_sharing_rule_id')->constrained('revenue_sharing_rules')->cascadeOnDelete();
            
            $table->string('transaction_type'); // Tithe, Offering, Project
            $table->unsignedBigInteger('transaction_id');
            
            $table->foreignId('source_unit_id')->constrained('organizational_units')->cascadeOnDelete();
            $table->foreignId('destination_unit_id')->constrained('organizational_units')->cascadeOnDelete();

            $table->decimal('gross_amount', 12, 2);
            $table->decimal('distributed_amount', 12, 2);
            $table->string('calculation_summary');

            $table->enum('status', ['pending', 'settled', 'cancelled'])->default('pending');
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();

            $table->index(['transaction_type', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_distributions');
        Schema::dropIfExists('revenue_sharing_rules');
    }
};
