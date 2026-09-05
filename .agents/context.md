# AI Agent Context & Project Overview

## Project Identity
- **Name**: Church SaaS Management Platform
- **Architecture**: Multi-Tenant SaaS with `stancl/tenancy v4` (Separate DB per Tenant)
- **Tech Stack**: Laravel 13 / PHP 8.4, Vue 3 / Composition API (`<script setup lang="ts">`), Inertia.js, TypeScript, Tailwind CSS, PrimeVue, MySQL, Pest testing framework.

## Project Structure Overview

```
.agents/
├── agents/            # Specialized AI agent roles & responsibilities
├── skills/            # Actionable implementation playbooks
├── rules/             # Agent workflow rules
└── context.md         # System overview & agent orientation

docs/
└── knowledge/         # Architectural knowledge base & system standards
```

## Architectural Guidelines Summary
1. **Tenancy**: Never run cross-database queries between Central (`church_central`) and Tenant databases (`church_tenant_*`).
2. **Backend**: Strict PHP 8.4 (`declare(strict_types=1);`). Controllers are thin; logic lives in Services and Repositories. Mutations require FormRequests.
3. **Frontend**: Vue 3 `<script setup lang="ts">`. No `any` types. PrimeVue UI components styled with Tailwind CSS.
4. **Testing**: Feature & Tenant tests written in Pest PHP.

Before writing code, AI agents MUST consult the relevant file in `docs/knowledge/` and follow the execution steps in `.agents/skills/`.
