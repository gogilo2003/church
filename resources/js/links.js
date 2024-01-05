import { ref } from 'vue'

export const links = ref([
    {
        name: "dashboard",
        caption: "Dashboard",
        icon: "dashboard",
        show: true,
        permission: 0,
    },
    {
        name: "users",
        caption: "Users",
        icon: "people",
        show: true,
        permission: 1,
    },
    {
        name: "members",
        caption: "Members",
        icon: "id-card",
        show: true,
        permission: 0,
    },
    {
        name: "attendance",
        caption: "Attendance",
        icon: "attendance",
        show: true,
        permission: 0,
    },
    // {
    //     name: "setup-departments",
    //     caption: "Departments",
    //     icon: "location",
    //     show: true,
    //     permission: 0,
    // },
    {
        name: "contributions",
        caption: "Contributions",
        icon: "money",
        show: true,
        permission: 0,
    },
])

export const linksBottom = ref([
    {
        name: "profile.show",
        caption: "Profile",
        icon: "people",
        show: true,
        permission: 0,
    },
    {
        name: "logout",
        caption: "Logout",
        icon: "logout",
        show: true,
        permission: 0,
        as: 'button',
        method: 'post'
    },
])
