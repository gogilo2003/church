# Coding Standards & Design Conventions

## PHP 8.3+ & Laravel Standards

### PHP Features Usage
- **Strict Types**: Every PHP file MUST start with `declare(strict_types=1);`.
- **Constructor Property Promotion**: Always use constructor property promotion with `private readonly` properties in Services, Repositories, and Actions.
- **Match Expressions**: Prefer `match` expressions over `switch` statements.
- **Enums**: Use Backed Enums (`string` backed) for all domain statuses and static types (`MemberStatus`, `PaymentMethod`, `SmsStatus`).
- **PHP 8.4 Features**: Avoid PHP 8.4-specific syntax (e.g. property hooks or asymmetric visibility) until the execution runtime environment is upgraded past PHP 8.3.

### Code Formatting & Verification
- Follow **PSR-12** standards strictly.
- Format code with `composer pint`.
- Verify full project compliance (Pint + TypeScript) with `composer check`.

```php
declare(strict_types=1);

namespace App\Services;

use App\Enums\MemberStatus;
use App\Repositories\Contracts\MemberRepositoryInterface;

final class MemberService
{
    public function __construct(
        private readonly MemberRepositoryInterface $memberRepository,
    ) {}

    public function activateMember(int $id): bool
    {
        return $this->memberRepository->updateStatus($id, MemberStatus::Active);
    }
}
```

## TypeScript & Vue 3 Standards

### Vue Component Authoring
- Always use `<script setup lang="ts">`.
- Strict typing for `defineProps<{ ... }>()` and `defineEmits<{ ... }>()`.
- Avoid `any` type under all circumstances. Define interfaces in `resources/js/types/`.
- Use Composition API composables (`useForm`, `usePage`, custom composables).

```vue
<script setup lang="ts">
import { computed } from 'vue';
import { Member } from '@/types';
import Button from 'primevue/button';

const props = defineProps<{
    member: Member;
    canEdit?: boolean;
}>();

const emit = defineEmits<{
    (e: 'select', id: number): void;
    (e: 'update:status', status: string): void;
}>();

const fullName = computed(() => `${props.member.first_name} ${props.member.last_name}`);
</script>

<template>
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ fullName }}</h3>
        <Button 
            v-if="canEdit" 
            label="Select Member" 
            icon="pi pi-check" 
            class="p-button-sm mt-2" 
            @click="emit('select', member.id)" 
        />
    </div>
</template>
```

## Naming Conventions
- **Controllers**: Singular noun + `Controller` (`MemberController.php`). Maximum 15 lines per method.
- **Services**: Singular noun + `Service` (`AttendanceService.php`).
- **Repositories**: `Interface` suffix for contract in `App\Repositories\Contracts\` (`MemberRepositoryInterface.php`), `Repository` suffix for implementation in `App\Repositories\Eloquent\` (`MemberRepository.php`).
- **FormRequests**: Action + Resource + `Request` (`StoreMemberRequest.php`, `UpdateTitheRequest.php`).
- **Policies**: Resource + `Policy` (`MemberPolicy.php`).
- **Vue Components**: PascalCase (`MemberCard.vue`, `SelectInput.vue`).
- **Vue Pages**: Organized in domain folders (`Pages/Members/Index.vue`, `Pages/Tithes/Create.vue`).
