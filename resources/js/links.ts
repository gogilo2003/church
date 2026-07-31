import { ref, Ref } from 'vue'
import { SidebarLink } from './types'

export const tenantLinks: Ref<SidebarLink[]> = ref([
    {
        name: "dashboard",
        caption: "Church Dashboard",
        icon: "dashboard",
        items: null,
        show: true,
        permission: 0,
    },
    {
        name: "users",
        caption: "Users",
        icon: "people",
        items: null,
        show: true,
        permission: 1,
    },
    {
        name: "members",
        caption: "Members",
        icon: "id-card",
        items: null,
        show: true,
        permission: 0,
    },
    {
        name: "attendance",
        caption: "Attendance",
        icon: "attendance",
        items: null,
        show: true,
        permission: 0,
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
        permission: 0,
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
        permission: 0,
    },
])

export const centralAdminLinks: Ref<SidebarLink[]> = ref([
    {
        name: "central.admin.dashboard",
        caption: "Central Dashboard",
        icon: "dashboard",
        items: null,
        show: true,
        permission: 1,
    },
    {
        name: "central.admin.tenants.index",
        caption: "Tenant Management",
        icon: "home",
        items: null,
        show: true,
        permission: 1,
    },
    {
        name: "central.register-tenant.create",
        caption: "Register Church",
        icon: "add",
        items: null,
        show: true,
        permission: 1,
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
        permission: 0,
    },
    {
        name: "logout",
        caption: "Logout",
        icon: "logout",
        items: null,
        show: true,
        permission: 0,
        as: 'button',
        method: 'post'
    },
])
