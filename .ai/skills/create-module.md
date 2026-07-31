# Skill Playbook: Create Module

## Objective
Scaffold a new domain module (e.g., Groups, Events, Assets) within the Church SaaS application architecture.

## Scaffolding Checklist
1. Create Database Migration in `database/migrations/tenant/`.
2. Create Eloquent Model in `app/Models/Tenant/`.
3. Create Policy in `app/Policies/Tenant/`.
4. Create DTO (Data Transfer Object) in `app/DataTransferObjects/`.
5. Create Repository Interface and Implementation in `app/Repositories/`.
6. Create Service Class in `app/Services/Tenant/`.
7. Create FormRequests (`Store...Request`, `Update...Request`) in `app/Http/Requests/Tenant/`.
8. Create Controller in `app/Http/Controllers/Tenant/`.
9. Add Route definitions in `routes/tenant.php`.
10. Create Vue 3 Pages (`Index.vue`, `Create.vue`, `Edit.vue`, `Show.vue`) in `resources/js/Pages/{Module}/`.
11. Update TypeScript interfaces in `resources/js/types/index.d.ts`.
12. Create Pest Test suite in `tests/Feature/Tenant/{Module}Test.php`.
