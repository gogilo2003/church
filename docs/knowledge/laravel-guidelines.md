# Laravel 13 Best Practices & Architectural Guidelines

## Layered Architecture Pattern

```
HttpRequest
   │
   ▼
Route ──► FormRequest (Validation & Authorization)
             │
             ▼
        Controller (HTTP Orchestration only)
             │
             ▼
        Service Layer (Business Logic & Transactions)
             │
             ▼
      Repository Layer (Database Queries & Scopes)
             │
             ▼
       Eloquent Model (Schema & Relationships)
```

## Rules for Layers

### 1. Controllers (`app/Http/Controllers/`)
- Controllers MUST be thin (max 15-20 lines per method).
- MUST return `Inertia::render()` for web pages or `JsonResponse` for API endpoints.
- MUST NOT contain direct DB queries or raw business calculations.

### 2. Validation (`app/Http/Requests/`)
- NEVER validate inline inside controller methods `$request->validate()`.
- Create dedicated `FormRequest` classes.
- Implement both `authorize(): bool` and `rules(): array`.

### 3. Service Layer (`app/Services/`)
- Service methods MUST encapsulate complete use-cases.
- Use `DB::transaction()` for operations modifying multiple tables.
- Dispatch Events from services (`MemberRegistered`, `TitheRecorded`).

```php
declare(strict_types=1);

namespace App\Services\Tenant;

use App\DataTransferObjects\TitheData;
use App\Events\Tenant\TitheRecorded;
use App\Repositories\Contracts\TitheRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class TitheService
{
    public function __construct(
        private readonly TitheRepositoryInterface $titheRepository,
    ) {}

    public function recordTithe(TitheData $data, int $userId): mixed
    {
        return DB::transaction(function () use ($data, $userId) {
            $tithe = $this->titheRepository->create([
                'member_id' => $data->memberId,
                'tithed_on' => $data->tithedOn,
                'amount' => $data->amount,
                'payment_method' => $data->paymentMethod,
                'reference_no' => $data->referenceNo,
                'recorded_by_user_id' => $userId,
            ]);

            event(new TitheRecorded($tithe));

            return $tithe;
        });
    }
}
```

### 4. Repository Layer (`app/Repositories/`)
- Interfaces reside in `App\Repositories\Contracts\`.
- Implementations reside in `App\Repositories\Eloquent\`.
- Bind interfaces in `App\Providers\RepositoryServiceProvider`.

### 5. Policies & Authorization (`app/Policies/`)
- Every Eloquent model in the Tenant domain MUST have a corresponding Policy.
- Check policies using `$this->authorize('update', $member)` or via FormRequests.
