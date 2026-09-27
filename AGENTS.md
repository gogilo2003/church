# AI Agent Guidelines for Church SaaS

## Project Identity
- **Name**: Church SaaS Management Platform
- **Architecture**: Multi-Tenant SaaS with `stancl/tenancy v3` (Separate Database per Tenant)
- **Tech Stack**: Laravel 13 / PHP 8.3+, Vue 3 (Composition API, `<script setup lang="ts">`), Inertia.js, TypeScript, Tailwind CSS, PrimeVue, MySQL, Pest testing framework
- **Development Domains**: Central platform at `church.test` (`env('CENTRAL_DOMAIN', 'church.test')`); tenants at `{subdomain}.church.test` (e.g. `ack.church.test`)

---

## Strict Rules & Invariants
1. **Tenancy Isolation**:
   - NEVER execute cross-database queries or JOINs between Central DB (`church_central`) and Tenant DBs (`church_tenant_*`).
   - Central models reside in `App\Models\Central\*` and use central database connection.
   - Tenant models reside in `App\Models\*` and run inside initialized tenant database connection.
2. **Layered Architecture**:
   - **Controllers**: MUST be thin (maximum 15 lines per method). Only handle HTTP input, delegate to services, and return responses.
   - **FormRequests**: All mutations (POST/PUT/PATCH/DELETE) MUST be validated and authorized through dedicated `FormRequest` classes.
   - **Services**: Business logic, external integrations, and multi-step transactions MUST reside in `app/Services/`.
   - **Repositories**: Data access and complex queries MUST go through `app/Repositories/` interfaces.
3. **Type Safety**:
   - PHP files MUST begin with `declare(strict_types=1);`.
   - TypeScript Vue components MUST use `<script setup lang="ts">`. NEVER use explicit `any`.
4. **Authentication & Routing Separation**:
   - Central staff auth is at `/admin/login` (`central.admin.login`). Central `/login` is an alias (`central.login`) redirecting to `/admin/login`.
   - Tenant user auth is at `/login` on tenant subdomains (`{tenant}.church.test/login`).
   - Do NOT hardcode central domains in route helpers.
5. **Verification**:
   - Always run `composer check` (executes `vendor/bin/pint --test` and `npm run type-check`).
   - Always verify PHP backend changes with `php artisan test` or `./vendor/bin/pest`.
6. **Version Control & Commits**:
   - **Always Commit Changes**: Always commit changes upon completing a task or iteration.
   - **Conventional Commits**: Use concise commit messages (`feat:`, `fix:`, `refactor:`, `style:`, `docs:`, `test:`, `chore:`).

---

## Knowledge Base Map

### System Specifications (`docs/knowledge/`)
- **System Architecture**: [`docs/knowledge/architecture.md`](file:///home/ogilo/Projects/church/current/docs/knowledge/architecture.md)
- **Multi-Tenancy Specs**: [`docs/knowledge/tenancy.md`](file:///home/ogilo/Projects/church/current/docs/knowledge/tenancy.md)
- **Coding Standards**: [`docs/knowledge/coding-standards.md`](file:///home/ogilo/Projects/church/current/docs/knowledge/coding-standards.md)
- **Domain Model**: [`docs/knowledge/domain-model.md`](file:///home/ogilo/Projects/church/current/docs/knowledge/domain-model.md)
- **Permissions & Roles**: [`docs/knowledge/permissions.md`](file:///home/ogilo/Projects/church/current/docs/knowledge/permissions.md)
- **UI & PrimeVue Patterns**: [`docs/knowledge/ui-patterns.md`](file:///home/ogilo/Projects/church/current/docs/knowledge/ui-patterns.md)

### Implementation Playbooks (`.agents/skills/`)
- **Backend**: `create-controller.md`, `create-service.md`, `create-repository.md`, `create-request.md`, `create-policy.md`, `create-migration.md`
- **Frontend**: `create-vue-page.md`, `create-form.md`
- **Testing & Review**: `testing.md`, `review-code.md`, `refactor.md`, `debug.md`

Before writing code, consult the relevant specification in `docs/knowledge/` and follow the execution steps in `.agents/skills/`.