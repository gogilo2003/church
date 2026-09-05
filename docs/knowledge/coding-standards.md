# Coding Standards & Design Conventions

## PHP 8.4 & Laravel Standards

### PHP Features Usage
- **Strict Types**: Every PHP file MUST start with `declare(strict_types=1);`.
- **Property Hooks & Asymmetric Visibility**: Utilize PHP 8.4 property hooks where beneficial for calculated attributes.
- **Constructor Property Promotion**: Always use constructor property promotion in Services, Repositories, and Actions.
- **Match Expressions**: Prefer `match` expressions over `switch` statements.
- **Enums**: Use Backed Enums (`string` backed) for all domain statuses and static types (`MemberStatus`, `PaymentMethod`, `SmsStatus`).

### Code Formatting
- Follow **PSR-12** standards strictly.
- Run `composer lint` (Pint) before committing.

```php
declare(strict_types=1);

namespace App\Services\Tenant;

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
- **Controllers**: Singular noun + `Controller` (`MemberController.php`).
- **Services**: Singular noun + `Service` (`AttendanceService.php`).
- **Repositories**: `Interface` suffix for contract (`MemberRepositoryInterface.php`), `Repository` suffix for implementation (`MemberRepository.php`).
- **FormRequests**: Action + Resource + `Request` (`StoreMemberRequest.php`, `UpdateTitheRequest.php`).
- **Policies**: Resource + `Policy` (`MemberPolicy.php`).
- **Vue Components**: PascalCase (`MemberCard.vue`, `SelectInput.vue`).
- **Vue Pages**: Organized in domain folders (`Pages/Members/Index.vue`, `Pages/Tithes/Create.vue`).
