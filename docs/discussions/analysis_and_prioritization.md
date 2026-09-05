# Church SaaS: Gap Analysis, Comparison & Feature Prioritization Roadmap

## Executive Summary

The User Requirements Specification (`.ai/discussions/notes.md`) outlines a vision for an **all-in-one Church Operations & Ministry Ecosystem**. It elevates the platform from a standard local-church tracking tool into a multi-tier, hierarchical SaaS platform capable of managing independent local congregations as well as large denominational structures (e.g., General Council → Diocese → District → Parish → Local Church).

---

## 1. What We Currently Have in Place

Our existing codebase already provides a solid, multi-tenant foundation built on **Laravel + Inertia.js (Vue 3) + TailwindCSS**. 

### Existing Core Modules & Architecture

| Category | Implemented Components | Details |
| :--- | :--- | :--- |
| **Multi-Tenancy & Auth** | `stancl/tenancy`, `Tenant`, `Domain`, `User`, `Role` | Subdomain-based tenant isolation, user role assignment. |
| **Organizational Hierarchy** | `Organization`, `HierarchyDefinition`, `HierarchyLevel`, `OrganizationalUnit`, `OrgUnitClosure` | Closure table model supporting arbitrary tree structures (Regions, Dioceses, Districts, Parishes). |
| **Member Management** | `Member`, `Department`, `Group` | Member profiles, phone, email, status, department & group assignments. |
| **Attendance Tracking** | `Attendance`, `attendance_member` | Manual service attendance logging for members and visitors. |
| **Financials & Contributions** | `Tithe`, `Offering`, `OfferingType`, `Contribution`, `ContributionType`, `Payment` | Basic logging of tithes, offerings, special pledges, and payment records. |
| **Communication** | `Sms`, `MemberSms` | SMS broadcast queue, member mapping, retry logic. |
| **Assets & Projects** | `Project`, `Institution`, `Assignment` | Basic project & institution structure. |

---

## 2. Comparison Matrix: Current System vs. `notes.md`

| Feature Domain | Current System (`/app`) | Specification (`notes.md`) | Gap Status |
| :--- | :--- | :--- | :--- |
| **Hierarchy & Multi-Tier** | Models created (`OrganizationalUnit`, closure tree), partial UI | Full RBAC scoped by jurisdiction (e.g., Bishop sees Diocese, Pastor sees Local Church) | 🟡 **In Progress / Partial** |
| **Member Lifecycle** | Basic status (Active/Inactive), simple fields | Detailed lifecycle (Visitor → Convert → Baptized → Confirmed → Active → Transferred), photo ID, spiritual gifts, custom fields | 🔴 **Major Gaps** |
| **Family & Household** | No explicit `Family` or `Household` model | Household creation, parent-child links, family attendance & giving statements | 🔴 **Missing** |
| **Visitor Management** | Logged only as count in Attendance | Full visitor pipeline: prayer requests, assigned follow-up leader, conversion tracking | 🔴 **Missing** |
| **Pastoral Care** | None | Counseling notes, home/hospital visits, funeral/marriage care, confidential notes | 🔴 **Missing** |
| **Cell / Small Groups** | `Group` table exists for general groups | Small group meeting logs, cell attendance, cell growth metrics, Bible study material | 🟡 **Basic Only** |
| **Children & Youth Ministry** | No specialized logic | Security check-in/check-out, allergy notes, parent linkage, classroom assignments | 🔴 **Missing** |
| **Advanced Finance & Accounting**| Simple tithe/offering entry tables | Chart of accounts, journal entries, cashbook, bank reconciliation, budgets, recurring giving | 🔴 **Major Gaps** |
| **Facility & Asset Booking** | `Project` & `Institution` basic tables | Booking sanctuary/rooms/vehicles, maintenance schedules, depreciation | 🔴 **Missing** |
| **Member Self-Service Portal** | Basic auth profile edit | Member portal: event registration, giving history, prayer requests, course progress | 🔴 **Missing** |
| **Mobile App & Integrations** | Web responsive | Mobile apps (iOS/Android), Mobile Money (M-Pesa/Airtel), WhatsApp, Live Streaming | 🔴 **Missing** |

---

## 3. Can `notes.md` Improve Our Current System?

**Yes, significantly.** 

1. **Unlocks Enterprise / Denominational SaaS Market**: Current church software often targets single local churches. By fulfilling the hierarchical administration in `notes.md`, our SaaS can target national and global church denominations.
2. **Increases Member & Pastor Retention**: Adding Pastoral Care, Visitor Follow-up, and Cell Group tracking directly helps church leaders shepherd their congregation rather than just doing bookkeeping.
3. **Unlocks Automated Revenue**: Mobile Money (e.g., M-Pesa, MTN, Airtel) and online giving integrations increase tithe/offering collection ease for churches while allowing fee-based SaaS monetization.

---

## 4. Recommended Feature Prioritization Roadmap

To avoid building a monolithic, bloated application all at once, we recommend executing in **4 Strategic Phases**:

```
 ┌─────────────────────────────────────────────────────────┐
 │ PHASE 1: Core Foundation & Operational Essentials       │
 │ (Member Lifecycle, Family Units, Visitor Pipeline)      │
 └───────────────────────────┬─────────────────────────────┘
                             │
 ┌───────────────────────────▼─────────────────────────────┐
 │ PHASE 2: Ministry Growth & Financial Deepening          │
 │ (Cell Groups, Pastoral Care, Mobile Money & Online Giving)│
 └───────────────────────────┬─────────────────────────────┘
                             │
 ┌───────────────────────────▼─────────────────────────────┐
 │ PHASE 3: Hierarchy, RBAC & Children/Youth Security      │
 │ (Denominational Rollup, Child Check-in/Out, Facilities) │
 └───────────────────────────┬─────────────────────────────┘
                             │
 ┌───────────────────────────▼─────────────────────────────┐
 │ PHASE 4: Ecosystem, Portal & Mobile Expansion           │
 │ (Member Self-Service Portal, iOS/Android App, WhatsApp) │
 └───────────────────────────┘
```

### Phase 1: Core Foundation & Operational Essentials (Immediate Impact)
*Goal: Solidify local church day-to-day operations.*
* **1.1 Member Lifecycle & Expanded Profile**: Add baptism status, conversion date, spiritual gifts, photo upload.
* **1.2 Household / Family Management**: Create `families` table, link parents and children, enable family-level view.
* **1.3 Visitor & Follow-up Pipeline**: Dedicated Visitor registration page, assign follow-up leaders, track conversion to membership.
* **1.4 Enhanced Attendance**: Support service-level attendance breakdown (men, women, children, visitors).

### Phase 2: Ministry Shepherding & Financial Deepening (High Value for Pastors & Treasurers)
*Goal: Provide pastors with care tools and treasurers with seamless financial logging.*
* **2.1 Cell / Small Group Module**: Small group leader dashboards, meeting reporting, cell attendance.
* **2.2 Pastoral Care & Confidential Counseling**: Pastoral visit logs, counseling records with strict role privacy.
* **2.3 Mobile Money & Payment Gateway Integration**: M-Pesa / Stripe / Paystack integration for online tithes & offerings.
* **2.4 Giving Statements & Receipts**: Automated PDF receipts and annual tax giving statements for members.

### Phase 3: Denominational Hierarchy, Security & Facility Management
*Goal: Enable multi-branch and denominational oversight.*
* **3.1 Scoped Jurisdiction RBAC**: Enforce data scoping across regions, dioceses, and parishes using existing `OrgUnitClosure`.
* **3.2 Children's Ministry & Security Check-In**: Parent claim tags, allergy alerts, classroom rosters.
* **3.3 Facility & Resource Booking**: Sanctuary, meeting room, and vehicle booking calendar with conflict prevention.
* **3.4 Workflow Approvals**: Requisition and expense approval workflows for treasurers and senior pastors.

### Phase 4: Self-Service Member Portal & Mobile Ecosystem
*Goal: Empower congregation engagement.*
* **4.1 Member Self-Service Portal**: Member dashboard to view giving history, update profile, join groups, submit prayer requests.
* **4.2 Multichannel Communication**: Add WhatsApp Business API and automated email notifications alongside SMS.
* **4.3 Discipleship & Class Tracking**: Track new believers' classes, baptism classes, and leadership courses.
* **4.4 Native Mobile Application**: Flutter / React Native app consuming the Laravel API.

---

## 5. Next Action Plan

1. **Store Analysis File**: Created `.ai/discussions/analysis_and_prioritization.md` for team alignment.
2. **Begin Phase 1 Implementation**: Start with **Member Lifecycle**, **Family / Household Relationships**, and **Visitor Follow-up Pipeline**.
