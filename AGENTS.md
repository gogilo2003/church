# AI Agent Guidelines for Church SaaS

## Project Identity
- **Name**: Church SaaS Management Platform
- **Architecture**: Multi-Tenant SaaS with `stancl/tenancy v4` (Separate DB per Tenant)
- **Tech Stack**: Laravel 13 / PHP 8.4, Vue 3 (Composition API, `<script setup lang="ts">`), Inertia.js, TypeScript, Tailwind CSS, PrimeVue, MySQL, Pest testing framework

## Strict Rules
1. **Tenancy Isolation**: NEVER run cross-database queries or JOINs between the Central DB (`church_central`) and Tenant DBs (`church_tenant_*`).
2. **Layered Architecture**:
   - Controllers MUST be thin (max 15 lines per method).
   - All mutations MUST go through `FormRequest` classes.
   - Business logic & transactions MUST reside in `app/Services/`.
   - Data access MUST use `app/Repositories/` interfaces.
3. **Type Safety**:
   - PHP files MUST include `declare(strict_types=1);`.
   - TypeScript Vue components MUST use `<script setup lang="ts">`. NEVER use explicit `any`.
4. **Verification**:
   - Always verify frontend changes with `npm run type-check`.
   - Always verify PHP backend changes with `php artisan test` or `vendor/bin/pint --test`.

## Knowledge Base
- System Architecture: `docs/knowledge/architecture.md`
- Multi-Tenancy Specs: `docs/knowledge/tenancy.md`
- Coding Standards: `docs/knowledge/coding-standards.md`
- Domain Model: `docs/knowledge/domain-model.md`
- Other Docs & Specs: `docs/knowledge/`
- Implementation Playbooks: `.agents/skills/`
- Agent Roles: `.agents/agents/`
- Workflow Rules: `.agents/rules/`
- Full AI Context: `.agents/context.md`

Before writing code, consult the relevant file in `docs/knowledge/` and follow the execution steps in `.agents/skills/`.

## Version Control & Commits
- **Always Commit Changes**: Always commit changes to the codebase upon task completion.
- **Conventional Commits**: Use clear conventional commit messages (e.g., `feat:`, `fix:`, `refactor:`, `chore:`).