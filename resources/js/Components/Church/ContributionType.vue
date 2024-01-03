<script lang="ts" setup>
import { formatCurrency, formatDate } from '../../helpers';

defineProps({
    item: Object
})
</script>
<template>
    <div>
        <div class="text-base uppercase font-semibold" v-text="item?.description"></div>
        <div class="flex gap-2 divide-x divide-gray-500 text-xs font-medium text-gray-500 capitalize [&>*]:pl-2">
            <span class="first:pl-0" v-if="item?.recurrent"
                v-text="`every ${item?.recurrence_value} ${item?.recurrence_unit}${item?.recurrence_value > 1 ? 's' : ''}`"></span>
            <span class="first:pl-0" v-if="item?.amount" v-text="`Amount: ${formatCurrency(item?.amount)}`"></span>
            <span v-if="!item?.amount" v-text="`Custom Amount`"></span>
            <span class="first:pl-0" v-if="item?.deadline" v-text="`Closes at: ${formatDate(item?.deadline)}`"></span>
            <span class="first:pl-0" v-if="!item?.deadline && item?.recurrence_unit"
                v-text="`Closes at end every ${item?.recurrence_unit}`"></span>
        </div>
    </div>
</template>
