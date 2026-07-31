# Project Glossary & Ubiquitous Language

| Term | Definition |
| :--- | :--- |
| **Central App** | The root application hosting landing pages, onboarding, billing, and global administration (`churchsaas.com`). |
| **Tenant App** | An isolated church application instance running on a subdomain or custom domain (`grace.churchsaas.com`). |
| **Tenant** | A customer church entity registered in the system. |
| **Domain** | A web hostname linked to a Tenant (subdomain or custom apex/sub domain). |
| **Member** | A registered congregant in a specific church database. |
| **Attendance Session** | A recorded church service or gathering event with logged attendee members. |
| **Tithe** | A recorded financial contribution given by a member. |
| **Offering** | A recorded non-person-specific or general financial collection for a specific offering type. |
| **Contribution Campaign** | A pledge or building fund campaign with target amounts and tracking balances. |
| **SMS Broadcast** | Bulk messaging dispatched to filtered members via an integrated SMS gateway. |
| **RBAC** | Role-Based Access Control system assigning permissions to church staff. |
| **Tenancy Initialization** | The request lifecycle phase where Laravel switches runtime database connections, storage paths, and caches to a target tenant. |
