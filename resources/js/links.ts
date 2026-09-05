import { ref, Ref } from 'vue'
import { SidebarLink } from './types'

export const tenantLinks: Ref<SidebarLink[]> = ref([
    {
        name: "dashboard",
        caption: "Dashboard",
        icon: "dashboard",
        items: null,
        show: true,
    },
    {
        name: "users",
        caption: "Users & Roles",
        icon: "people",
        items: [
            {
                name: "users.index",
                caption: "Users List",
            },
            {
                name: "roles.index",
                caption: "Role Management",
            },
        ],
        show: true,
    },
    {
        name: "members",
        caption: "Members & Families",
        icon: "id-card",
        items: [
            {
                name: "members.index",
                caption: "Member Directory",
            },
            {
                name: "households.index",
                caption: "Households & Families",
            },
            {
                name: "visitors.index",
                caption: "Visitor Follow-up",
            },
        ],
        show: true,
        permission: "members.view",
    },
    {
        name: "attendance",
        caption: "Attendance",
        icon: "attendance",
        items: null,
        show: true,
        permission: "attendance.view",
    },
    {
        name: "messaging",
        caption: "Messaging Centre",
        icon: "message",
        items: [
            {
                name: "messaging-sms",
                caption: "SMS Messages",
            },
        ],
        show: true,
        permission: "sms.send",
    },
    {
        name: "accounts",
        caption: "Accounts",
        icon: "money",
        items: [
            {
                name: "accounts-offerings",
                caption: "Offerings",
            },
            {
                name: "accounts-tithes",
                caption: "Tithes",
            },
            {
                name: "accounts-contributions",
                caption: "Contributions",
            },
        ],
        show: true,
        permission: "accounts.view",
    },
    {
        name: "setup",
        caption: "Setup & Config",
        icon: "settings",
        items: [
            {
                name: "setup-departments",
                caption: "Departments",
            },
            {
                name: "setup-organization",
                caption: "Organization Hierarchy",
            },
        ],
        show: true,
        permission: "organization.view",
    },
])

export const centralAdminLinks: Ref<SidebarLink[]> = ref([
    {
        name: "central.admin.dashboard",
        caption: "Central Dashboard",
        icon: "dashboard",
        items: null,
        show: true,
        permission: "admin",
    },
    {
        name: "central.admin.tenants.index",
        caption: "Tenant Management",
        icon: "home",
        items: null,
        show: true,
        permission: "admin",
    },
    {
        name: "central.register-tenant.create",
        caption: "Register Church",
        icon: "add",
        items: null,
        show: true,
        permission: "admin",
    },
])

// Backwards compatibility alias
export const links = tenantLinks

export const linksBottom: Ref<SidebarLink[]> = ref([
    {
        name: "profile.edit",
        caption: "Profile",
        icon: "people",
        items: null,
        show: true,
    },
    {
        name: "logout",
        caption: "Logout",
        icon: "logout",
        items: null,
        show: true,
        as: 'button',
        method: 'post'
    },
])
