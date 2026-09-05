# Skill Playbook: Create Feature

## Objective
Implement a full end-to-end feature spanning the backend (Migration, Model, Repository, Service, FormRequest, Controller) and frontend (Vue 3 Inertia page, PrimeVue components, TypeScript interfaces).

## Step-by-Step Execution Plan

### Step 1: Database Migration
- Determine target scope: Central (`database/migrations/`) or Tenant (`database/migrations/tenant/`).
- Create migration using `php artisan make:migration create_{table}_table`.

### Step 2: Model Definition
- Place model in `App\Models\Central\` or `App\Models\Tenant\`.
- Define `$fillable` fields, casts (enums/dates/decimals), and typed relationship methods.

### Step 3: Repository Pattern
- Create Contract interface: `app/Repositories/Contracts/{Entity}RepositoryInterface.php`.
- Create Implementation: `app/Repositories/Eloquent/{Entity}Repository.php`.
- Register binding in `app/Providers/RepositoryServiceProvider.php`.

### Step 4: FormRequest Validation
- Create Request: `app/Http/Requests/Tenant/Store{Entity}Request.php`.
- Implement validation rules and Policy authorization check.

### Step 5: Service Layer
- Create Service: `app/Services/Tenant/{Entity}Service.php`.
- Implement business logic, database transactions (`DB::transaction`), and event dispatches.

### Step 6: Controller Implementation
- Create Controller: `app/Http/Controllers/Tenant/{Entity}Controller.php`.
- Call Service layer and return `Inertia::render('Domain/Page', $data)`.

### Step 7: Vue 3 Inertia Page
- Create page: `resources/js/Pages/{Domain}/{Page}.vue`.
- Write `<script setup lang="ts">`, define TypeScript prop interfaces, use PrimeVue UI components, and bind forms with `useForm()`.

### Step 8: Verification
- Run `npx vue-tsc --noEmit` to verify frontend typing.
- Run `php artisan test --filter={Entity}Test` to verify backend logic.
