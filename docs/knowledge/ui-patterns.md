# UI/UX Patterns & Layout Standards

## Dual Layout Architecture

### 1. Central Layout (`Layouts/CentralLayout.vue`)
- Tailored for public visitors, landing page, self-service church registration, and Central Admin Dashboard.
- Features top navigation bar, landing page hero components, clean footer, pricing table grids.

### 2. Tenant Layout (`Layouts/AppLayout.vue`)
- Designed for Church Admins, Pastors, Financial Officers, and Staff.
- Features:
  - Collapsible Sidebar with module links (Dashboard, Members, Attendance, Accounts/Tithes, Messaging, Settings).
  - Top Bar displaying current Church Name, Tenant Logo, User Profile Dropdown, Notifications Drawer, and Dark Mode Toggle.

```
┌────────────────────────────────────────────────────────────────────────┐
│ [Logo] Church Name                        [Notifications] [User Profile]│
├──────────────┬─────────────────────────────────────────────────────────┤
│ Sidebar      │ Main Content Area (Inertia Page Slot)                   │
│ - Dashboard  │ ┌─────────────────────────────────────────────────────┐ │
│ - Members    │ │ Page Header / Breadcrumbs / Actions                 │ │
│ - Attendance │ ├─────────────────────────────────────────────────────┤ │
│ - Accounts   │ │ Data Table / Form Grid / PrimeVue Dialogs            │ │
│ - Messaging  │ │                                                     │ │
│ - Settings   │ └─────────────────────────────────────────────────────┘ │
└──────────────┴─────────────────────────────────────────────────────────┘
```

## PrimeVue Data Table Standard
All list screens (Members, Tithes, Offerings, SMS Logs) MUST use standard PrimeVue DataTable wrappers with:
- Search input bar with debounce
- Column sorting
- Server-side Inertia pagination links
- Action dropdown buttons (View, Edit, Delete with SweetAlert2 confirmation)

## Form Design Standard
- Inline field validation error displays.
- Submit buttons with spinner indicator bound to `form.processing`.
- Modals for quick record creation/editing.
