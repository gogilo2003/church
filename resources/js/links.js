import { ref } from 'vue'

export const links = ref([
    {
        name: "dashboard",
        caption: "Dashboard",
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
    // {
    //     name: "setup-departments",
    //     caption: "Departments",
    //     icon: "location",
    //     items: null,
    //     show: true,
    //     permission: 0,
    // },
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

export const linksBottom = ref([
    {
        name: "profile.show",
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
