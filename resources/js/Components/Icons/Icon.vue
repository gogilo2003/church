<script setup>
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

defineProps(['type'])

defineOptions({
    inheritAttrs: false
})
</script>
<template>
    <Dashboard v-bind="$attrs" v-if="type == 'dashboard'" />
    <Product v-bind="$attrs" v-if="type == 'product'" />
    <Logout v-bind="$attrs" v-if="type == 'logout'" />
    <Add v-bind="$attrs" v-if="type == 'add'" />
    <Edit v-bind="$attrs" v-if="type == 'edit'" />
    <Times v-bind="$attrs" v-if="type == 'times'" />
    <Chart v-bind="$attrs" v-if="type == 'chart'" />
    <Location v-bind="$attrs" v-if="type == 'location'" />
    <Money v-bind="$attrs" v-if="type == 'money'" />
    <Home v-bind="$attrs" v-if="type == 'home'" />
    <Menu v-bind="$attrs" v-if="type == 'menu'" />
    <People v-bind="$attrs" v-if="type == 'people'" />
    <Settings v-bind="$attrs" v-if="type == 'settings'" />
    <Certificate v-bind="$attrs" v-if="type == 'certificate'" />
    <ChevronCircleBack v-bind="$attrs" v-if="type == 'chevron-circle-back'" />
    <ChevronCircleForward v-bind="$attrs" v-if="type == 'chevron-circle-forward'" />
    <Delete v-bind="$attrs" v-if="type == 'delete'" />
    <Image v-bind="$attrs" v-if="type == 'image'" />
    <IdCard v-bind="$attrs" v-if="type == 'id-card'" />
</template>
