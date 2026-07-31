# Skill Playbook: Create Migration

## Target Directory Identification
- **Central Migration**: `database/migrations/YYYY_MM_DD_HHMMSS_create_tablename_table.php`
- **Tenant Migration**: `database/migrations/tenant/YYYY_MM_DD_HHMMSS_create_tablename_table.php`

## Migration Template Standard
```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tithes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->date('tithed_on')->index();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->string('reference_no')->nullable()->index();
            $table->foreignId('recorded_by_user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tithes');
    }
};
```
