<script setup lang="ts">
import Modal from '../../Components/Modal.vue';
import SecondaryButton from '../../Components/SecondaryButton.vue';
import Icon from '../../Components/Icons/Icon.vue';

const props = defineProps({
    member: Object,
    show: { type: Boolean, default: false }
})

const emit = defineEmits(['close'])

const close = () => {
    emit('close')
}

</script>
<template>
    <Modal :show="show">
        <div class="p-6">
            <div class="flex items-center justify-between pb-3 mb-3 border-b text-gray-700">
                <h3 class=" text-lg font-semibold uppercase">Member Details</h3>
                <button @click="close">
                    <Icon type="times" class="h-5 w-5" />
                </button>
            </div>
            <div class="flex flex-col md:flex-row items-center gap-2">
                <div class="w-full md:w-56 h-56 border p-1 rounded-lg flex-none">
                    <img class="h-full w-full object-contain rounded" :src="member.photo_url">
                </div>
                <div class="flex-1 text-gray-700 w-full md:w-auto">
                    <div v-text="`${member.first_name} ${member.last_name}`"
                        class="border-b py-2 px-3 uppercase text-lg font-semibold"></div>
                    <div class="border-b py-2 px-3" v-text="member.phone"></div>
                    <div class="border-b py-2 px-3" v-text="member.email"></div>
                    <div class="border-b py-2 px-3" v-text="member.date_of_birth"></div>
                    <div class="border-b py-2 px-3" v-text="member.gender ? 'Female' : 'Male'"></div>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t">
                <SecondaryButton @click="close">Close</SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
