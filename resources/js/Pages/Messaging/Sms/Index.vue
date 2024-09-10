<script lang="ts" setup>
import AppLayout from "../../../Layouts/AppLayout.vue";
import Container from '../../../Components/Custom/Container.vue';
import Modal from "../../../Components/Modal.vue";
import Icon from '../../../Components/Icons/Icon.vue';
import { iSmsMessages, iRecipient, iNotification, iSms } from '../../../types';
import { ref, computed, watch } from 'vue';
import InputLabel from '../../../Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import SecondaryButton from '../../../Components/SecondaryButton.vue';
import Swal from 'sweetalert2';
import InputError from '../../../Components/InputError.vue';
import PrimaryButton from '../../../Components/PrimaryButton.vue';
import Paginator from "../../../Components/Paginator.vue";

const props = defineProps<{
    messages: iSmsMessages,
    recipients: iRecipient[],
    notification?: iNotification
}>()

const form = useForm<{
    id: number | null
    message: string | null
    recipients: number[] | any[]
}>({
    id: null,
    message: null,
    recipients: []
});

const title = computed(() => form.id ? 'Edit SMS' : 'Compose Sms');
const showDialog = ref(false)

const newSms = () => {
    form.reset();
    showDialog.value = true;
}

const editSms = () => {
    form.reset();
    showDialog.value = true;
}

const closeDialog = () => {
    form.reset();
    showDialog.value = false;
}

const submit = () => {
    if (form.id) {
        form.patch(route('messaging-sms-update', form.id), {
            preserveState: true,
            preserveScroll: true,
            only: ['messages', 'errors', 'notification'],
            onSuccess: () => {
                Swal.fire({
                    title: title.value,
                    text: props.notification.success ?? 'Message submitted successfully',
                    icon: 'success',
                })
            },
            onError: () => {
                Swal.fire({
                    title: title.value,
                    text: props.notification.danger ?? 'An error occurred while sending the sms! Please try again',
                    icon: 'error',
                })
            }
        })
    } else {
        form.post(route('messaging-sms-store'), {
            preserveState: true,
            preserveScroll: true,
            only: ['messages', 'errors', 'notification'],
            onSuccess: () => {
                Swal.fire({
                    title: title.value,
                    text: props.notification.success ?? 'Message submitted successfully',
                    icon: 'success',
                })
                closeDialog()
            },
            onError: () => {
                Swal.fire({
                    title: title.value,
                    text: props.notification.danger ?? 'An error occurred while sending the sms! Please try again',
                    icon: 'error',
                })
            }
        })
    }
}

const previousSelection = ref<iRecipient[]>([]);
const recipientsSearchQuery = ref('');

const filteredRecipients = computed(() => {
    const query = recipientsSearchQuery.value.toLowerCase();
    return props.recipients.filter(recipient =>
        recipient.name.toLowerCase().includes(query) ||
        recipient.phone.includes(query)
    );
});

const selectAll = ref(false);

const toggleSelectAll = () => {

    if (selectAll.value) {
        // Backup the current selection and select all filtered recipients
        previousSelection.value = [...form.recipients];
        form.recipients = [...form.recipients, ...filteredRecipients.value.map(item => item.id)];
    } else {
        // Restore the previous selection
        form.recipients = [...previousSelection.value];
    }
};

const showView = ref(false)
const selectedMessage = ref<iSms | null>(null)

const viewMessage = (message: iSms) => {
    showView.value = true
    selectedMessage.value = message
}

const closeView = () => {
    showView.value = false
    selectedMessage.value = null
}

const messageLength = computed(() => form.message?.length ?? 0)
const pages = computed(() => Math.ceil(messageLength.value / 160))
const numberOfMessages = computed(() => pages.value * form.recipients.length)

</script>
<template>
    <Modal :show="showView">
        <div class="flex items-center justify-between mx-3 py-2 mb-2 border-b">
            <div>View Message</div>
            <button @click="closeView">
                <Icon type="times" class="h-6 w-6 object-contain" />
            </button>
        </div>
        <div class="mx-3 my-3 pb-3">
            <div class="uppercase text-xs font-medium mb-2">Message</div>
            <div v-text="selectedMessage.message" class="text-sm text-gray-800 font-light bg-gray-100 p-2 rounded-lg">
            </div>
            <div class="text-sm flex flex-col gap-1 mt-3">
                <div class="uppercase text-xs font-medium">Sent to </div>
                <div class="flex gap-1 items-center text-gray-600 "
                    v-for="{ name, phone, status } in selectedMessage.recipients">
                    <Icon :type="status === 'Success' ? 'done' : 'close'" class="h-5 w-5 object-contain"
                        :class="{ 'text-lime-600': status === 'Success', 'text-orange-400': status === 'pending', 'text-red-600': status === 'failed' }" />
                    <span v-text="name"></span>
                    <span v-text="`(${phone})`"></span>
                </div>
            </div>
        </div>
    </Modal>
    <Modal :show="showDialog" max-width="3xl">
        <div class="flex items-center justify-between mx-3 py-2 mb-2 border-b">
            <div v-text="title"></div>
            <button @click="closeDialog">
                <Icon type="times" class="h-6 w-6 object-contain" />
            </button>
        </div>
        <div class="mx-3">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <InputLabel value="Message" />
                    <textarea id="message" rows="4" v-model="form.message"
                        class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder="Write your sms here..."></textarea>
                    <div v-show="messageLength" class="text-xs uppercase font-light text-gray-800"
                        v-text="`${messageLength} Characters, ${pages} Pages, ${numberOfMessages} Messages`">
                    </div>
                    <InputError :message="form.errors.message" />
                </div>
                <div class="mb-4">
                    <InputLabel value="Recipients" />
                    <div class="mb-4 flex items-center justify-between gap-6">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input v-model="recipientsSearchQuery" type="search" id="default-search"
                                class="block w-full p-2.5 ps-10 text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600  dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                                placeholder="Search recipients..." />
                        </div>
                        <div class="flex-none">
                            <div class="flex items-center">
                                <input id="recipient-all" type="checkbox" v-model="selectAll" @change="toggleSelectAll"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="recipient-all"
                                    class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-y-auto max-h-96">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700"
                                v-for="{ id, phone, name } in filteredRecipients">
                                <input v-model="form.recipients" :id="`recipient-${id}`" type="checkbox" :value="id"
                                    name="recipient"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label :for="`recipient-${id}`"
                                    class="w-full py-4 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    <div class="flex gap-2 items-center">
                                        <div v-text="name"></div>
                                        <div v-text="phone"></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4 flex items-center justify-between">
                    <PrimaryButton>
                        <Icon class="h-5 w-5 object-contain" type="send" />Send
                    </PrimaryButton>
                    <SecondaryButton @click.prevent="closeDialog">
                        <Icon class="h-5 w-5 object-contain" type="close" />Cancel
                    </SecondaryButton>
                </div>
            </form>
        </div>
    </Modal>
    <AppLayout title="SMS Messages">
        <template #header>
            <div class="flex items-center justify-between flex-1">
                <div>SMS Messages</div>
                <SecondaryButton @click="newSms">
                    <Icon class="h-5 w-5 object-contain" type="add" /> <span class="hidden md:inline-flex">New
                        SMS</span>
                </SecondaryButton>
            </div>
        </template>
        <Container>
            <div class="flex flex-col gap-2 mb-4">
                <div class="flex gap-5 px-3 py-1 items-center border-b mx-3" v-for="message in messages.data">
                    <div class="flex flex-col gap-1 flex-1">
                        <div v-text="message.message" class="line-clamp-2 text-sm text-gray-700"></div>
                        <div class="flex items-center gap-1 text-xs font-medium">
                            <div v-text="`Recipients: ${message?.recipients?.length}`"></div>
                            <div v-text="`Sent At: ${message.sent_at}`"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-none">
                        <SecondaryButton @click="viewMessage(message)">
                            <Icon class="h-5 w-5 object-contain" type="show" /><span
                                class="hidden md:inline-flex">View</span>
                        </SecondaryButton>
                    </div>
                </div>
            </div>

            <Paginator class="mx-3" :items="messages" />
        </Container>
    </AppLayout>
</template>
