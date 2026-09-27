# AI Agent Context & Project Overview

This repository uses [`AGENTS.md`](file:///home/ogilo/Projects/church/current/AGENTS.md) as the canonical instructions for all AI coding assistants.

## Project Identity
- **Name**: Church SaaS Management Platform
- **Architecture**: Multi-Tenant SaaS with `stancl/tenancy v3` (Separate Database per Tenant)
- **Tech Stack**: Laravel 13 / PHP 8.3+, Vue 3 / Composition API (`<script setup lang="ts">`), Inertia.js, TypeScript, Tailwind CSS, PrimeVue, MySQL, Pest testing framework.

## Project Structure Overview

```
.agents/
├── agents/            # Specialized agent role definitions
├── skills/            # Actionable implementation playbooks
├── rules/             # Agent workflow rules (e.g. git commit workflow)
└── context.md         # System overview & agent orientation

docs/
└── knowledge/         # Architectural knowledge base & system standards
```

## Architectural Guidelines Summary
1. **Tenancy**: Never run cross-database queries between Central (`church_central`) and Tenant databases (`church_tenant_*`). Central models in `App\Models\Central\*`, tenant models in `App\Models\*`.
2. **Backend**: Strict PHP 8.3+ (`declare(strict_types=1);`). Controllers are thin (< 15 lines/method); logic lives in Services and Repositories. Mutations require FormRequests.
3. **Frontend**: Vue 3 `<script setup lang="ts">`. No `any` types. PrimeVue UI components styled with Tailwind CSS.
4. **Verification**: Always run `composer check` and `php artisan test` before completing tasks.
5. **Version Control**: Always commit changes upon task completion using Conventional Commits.

Before writing code, consult the relevant specification in `docs/knowledge/` and follow the execution steps in `.agents/skills/`.
