# SaaS System Architecture

## Overview
This application is a multi-tenant Church Management SaaS built on **Laravel 13 / PHP 8.3+** and **Vue 3 / Inertia.js / TypeScript / PrimeVue / Tailwind CSS**. It provides a dual-application architecture separating the **Central Application** (landing, onboarding, tenant provisioning, subscription billing, system administration) from the **Tenant Application** (church operations: members, attendance, tithes, offerings, SMS messaging, departments, user roles & permissions).

```mermaid
graph TD
    Client[Client Browser / Mobile] --> Router{Domain & Subdomain Router}
    Router -->|church.test / CENTRAL_DOMAIN| CentralApp[Central Application]
    Router -->|ack.church.test / customdomain.org| TenantApp[Tenant Application]

    subgraph Central Architecture
        CentralApp --> CentralControllers[Central Controllers]
        CentralControllers --> CentralServices[Central Services]
        CentralServices --> CentralDB[(Central Database: church_central)]
    end

    subgraph Tenant Architecture (stancl/tenancy v3)
        TenantApp --> TenancyMiddleware[InitializeTenancyByDomainOrSubdomain]
        TenancyMiddleware --> TenantControllers[Tenant Controllers]
        TenantControllers --> TenantServices[Tenant Services]
        TenantServices --> TenantRepositories[Tenant Repositories]
        TenantRepositories --> TenantDB[(Tenant Database: church_tenant_id)]
    end
```

## Core Architectural Layers

1. **Routing & Tenancy Identification**: Handled via `stancl/tenancy v3`. Requests to central domain routes evaluate central logic; requests to tenant subdomains/custom domains initialize the tenancy context (switching default DB connection to `tenant`, setting storage path, and loading tenant configuration).
2. **Controller Layer**: Lean Inertia controllers (max 15 lines per method). Responsibilities are strictly limited to receiving HTTP requests, delegating validation to **FormRequests**, calling the **Service Layer**, and returning Inertia page responses or standard API payloads.
3. **Validation (FormRequest Layer)**: All mutation requests (POST/PUT/PATCH/DELETE) must pass through dedicated `FormRequest` classes containing validation rules and authorization calls.
4. **Service Layer**: Encapsulates business logic, database transactions, event dispatching, and external service calls (e.g., SMS gateways, payment processors).
5. **Repository Layer**: Abstraction layer over Eloquent queries. Handles complex filtering, pagination, search, and domain data access via contracts in `App\Repositories\Contracts`.
6. **Domain Models**: Eloquent models strictly bound to either the Central database (`App\Models\Central\*`) or Tenant database (`App\Models\*`).
7. **Frontend Pipeline**: Vue 3 SFCs using `<script setup lang="ts">`, Inertia.js page router, PrimeVue components, Tailwind CSS styling, and strict TypeScript interfaces without explicit `any`.

## Application Breakdown

### Central Application
- **Domain**: `church.test` (configured via `env('CENTRAL_DOMAIN', 'church.test')`)
- **Database**: `church_central`
- **Features**:
  - Landing pages & marketing
  - Self-service tenant registration & onboarding workflow
  - Central Super Admin console (`/admin/login`, `/admin`) & global tenant analytics
  - Tenant migration & domain mapping management
  - SMS delivery callbacks

### Tenant Application
- **Domain**: `{tenant}.church.test`, `{custom_domain}`
- **Database**: `church_tenant_{tenant_id}`
- **Features**:
  - Member management & directory
  - Attendance tracking & session analytics
  - Tithes, Offerings & Contribution campaigns
  - SMS & Email messaging center
  - Department & Ministry organization
  - Tenant user management & Role-Based Access Control (RBAC)
  - Custom branding & organization hierarchy
  - Audit logging & activity history
