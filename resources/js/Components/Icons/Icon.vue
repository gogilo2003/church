<script setup lang="ts">
import { computed } from 'vue';
import Dashboard from './Dashboard.vue';
import Product from './Product.vue';
import Logout from './Logout.vue';
import Add from './Add.vue';
import Edit from './Edit.vue';
import Times from './Times.vue';
import Chart from './Chart.vue';
import Location from './Location.vue';
import Money from './Money.vue';
import Home from './Home.vue';
import Menu from './Menu.vue';
import People from './People.vue';
import Settings from './Settings.vue';
import Certificate from './Certificate.vue';
import ChevronCircleBack from './ChevronCircleBack.vue';
import ChevronCircleForward from './ChevronCircleForward.vue';
import Delete from './Delete.vue';
import Image from './Image.vue';
import IdCard from './IdCard.vue';
import Show from './Show.vue';
import Attendance from './Attendance.vue';
import Checkmark from './Checkmark.vue';
import Done from './Done.vue';
import ArrowLeft from './ArrowLeft.vue';
import Download from './Download.vue';
import Message from './Message.vue';
import Close from './Close.vue';

const props = defineProps<{
    type?: string;
}>();

const iconMap: Record<string, any> = {
    dashboard: Dashboard,
    product: Product,
    logout: Logout,
    add: Add,
    edit: Edit,
    times: Times,
    chart: Chart,
    location: Location,
    money: Money,
    home: Home,
    menu: Menu,
    people: People,
    settings: Settings,
    certificate: Certificate,
    'chevron-circle-back': ChevronCircleBack,
    'chevron-circle-forward': ChevronCircleForward,
    delete: Delete,
    image: Image,
    'id-card': IdCard,
    show: Show,
    attendance: Attendance,
    checkmark: Checkmark,
    done: Done,
    'arrow-left': ArrowLeft,
    download: Download,
    message: Message,
    close: Close,
};

const icon = computed(() => {
    return props.type ? iconMap[props.type] : undefined;
});
</script>
<template>
    <component :is="icon" />
</template>
