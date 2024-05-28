<script setup>
import { computed } from 'vue';
import Dashboard from './Dashboard.vue'
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

const props = defineProps({
    type: String
})

const icon = computed(() => {
    if (props.type == 'dashboard') {
        return Dashboard;
    }
    if (props.type == 'product') {
        return Product;
    }
    if (props.type == 'logout') {
        return Logout;
    }
    if (props.type == 'add') {
        return Add;
    }
    if (props.type == 'edit') {
        return Edit;
    }
    if (props.type == 'times') {
        return Times;
    }
    if (props.type == 'chart') {
        return Chart;
    }
    if (props.type == 'location') {
        return Location;
    }
    if (props.type == 'money') {
        return Money;
    }
    if (props.type == 'home') {
        return Home;
    }
    if (props.type == 'menu') {
        return Menu;
    }
    if (props.type == 'people') {
        return People;
    }
    if (props.type == 'settings') {
        return Settings;
    }
    if (props.type == 'certificate') {
        return Certificate;
    }
    if (props.type == 'chevron-circle-back') {
        return ChevronCircleBack;
    }
    if (props.type == 'chevron-circle-forward') {
        return ChevronCircleForward;
    }
    if (props.type == 'delete') {
        return Delete;
    }
    if (props.type == 'image') {
        return Image;
    }
    if (props.type == 'id-card') {
        return IdCard;
    }
    if (props.type == 'show') {
        return Show;
    }
    if (props.type == 'attendance') {
        return Attendance;
    }
    if (props.type == 'checkmark') {
        return Checkmark;
    }
    if (props.type == 'done') {
        return Done;
    }
    if (props.type == 'arrow-left') {
        return ArrowLeft;
    }
})
</script>
<template>
    <component :is="icon" />
</template>
