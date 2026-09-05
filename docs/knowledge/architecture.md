# SaaS System Architecture

## Overview
This application is a multi-tenant Church Management SaaS built on **Laravel 13 / PHP 8.4** and **Vue 3 / Inertia.js / TypeScript / PrimeVue / Tailwind CSS**. It provides a dual-application architecture separating the **Central Application** (landing, onboarding, tenant provisioning, subscription billing, system administration) from the **Tenant Application** (church operations: members, attendance, tithes, offerings, SMS messaging, departments, user roles & permissions).

```mermaid
graph TD
    Client[Client Browser / Mobile] --> Router{Domain & Subdomain Router}
    Router -->|app.churchsaas.com / churchsaas.com| CentralApp[Central Application]
    Router -->|tenant1.churchsaas.com / customdomain.org| TenantApp[Tenant Application]

    subgraph Central Architecture
        CentralApp --> CentralControllers[Central Controllers]
        CentralControllers --> CentralServices[Central Services]
        CentralServices --> CentralDB[(Central Database)]
    end

    subgraph Tenant Architecture (stancl/tenancy v4)
        TenantApp --> TenancyMiddleware[Tenancy Initialization Middleware]
        TenancyMiddleware --> TenantControllers[Tenant Controllers]
        TenantControllers --> TenantServices[Tenant Services]
        TenantServices --> TenantRepositories[Tenant Repositories]
        TenantRepositories --> TenantDB[(Tenant Database N)]
    end
```

## Core Architectural Layers

1. **Routing & Tenancy Identification**: Handled via `stancl/tenancy v4`. Requests to central domain routes evaluate central logic; requests to tenant subdomains/custom domains initialize the tenancy context (switching default DB connection to `tenant`, setting storage path, and loading tenant configuration).
2. **Controller Layer**: Lean Inertia controllers. Responsibilities are strictly limited to receiving HTTP requests, delegating validation to **FormRequests**, calling the **Service Layer**, and returning Inertia page responses or standard API payloads.
3. **Validation (FormRequest Layer)**: All mutation requests (POST/PUT/PATCH/DELETE) must pass through dedicated `FormRequest` classes containing validation rules and authorization calls.
4. **Service Layer**: Encapsulates business logic, database transactions, event dispatching, and external service calls (e.g., SMS gateways, payment processors).
5. **Repository Layer**: Abstraction layer over Eloquent queries. Handles complex filtering, pagination, search, and domain data access.
6. **Domain Models**: Eloquent models strictly bound to either the Central database (`central` connection) or Tenant database (`tenant` connection).
7. **Frontend Pipeline**: Vue 3 SFCs using `<script setup lang="ts">`, Inertia.js page router, PrimeVue components, Tailwind CSS styling, and strict TypeScript interfaces.

## Application Breakdown

### Central Application
- **Domain**: `churchsaas.com`, `app.churchsaas.com`
- **Database**: `church_central`
- **Features**:
  - Landing pages & marketing
  - Self-service tenant registration & onboarding workflow
  - Subscription plan management (Tiering, Features, Limits)
  - Payment gateway webhooks (Stripe / Paystack / Flutterwave)
  - Domain mapping & SSL provision tracking
  - Central Super Admin console & global tenant analytics

### Tenant Application
- **Domain**: `{tenant}.churchsaas.com`, `{custom_domain}`
- **Database**: `church_tenant_{tenant_id}`
- **Features**:
  - Member management & directory
  - Attendance tracking & session analytics
  - Tithes, Offerings & Contribution campaigns
  - SMS & Email messaging center
  - Department & Ministry organization
  - Tenant user management & Role-Based Access Control (RBAC)
  - Custom branding (Logo, colors, primary church name)
  - Audit logging & activity history
