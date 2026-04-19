<template>
    <div>
        <Head title="Audit Payroll" />
        <h1 class="mb-8 font-bold text-3xl">Audit Payrolls</h1>
        <div class="mb-6 flex justify-between items-center">
            <div></div>
            <Link :href="route('audit_payroll.store')"
                  method="post"
                  class="btn btn-big btn-indigo"
                  as="button"
                  preserve-state>
                Add <span class="hidden md:inline"> &nbsp; Payroll</span>
            </Link>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div
                    class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Month
                            </th>
                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Created By
                            </th>

                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                &nbsp;
                            </th>

                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                &nbsp;
                            </th>

                            <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                Details
                            </th>
                        </tr>
                        </thead>
                        <tbody v-for="payroll in payrolls.data" :key="payroll.id" class="bg-white">
                        <tr :key="payroll.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
                            <td class="whitespace-no-wrap border-b border-gray-200">
                                <Link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                    <div class="px-6 pt-4 text-sm leading-5 font-medium text-gray-900 uppercase">
                                        {{ payroll.month }}
                                    </div>
                                    <div class="px-6 pb-4 text-sm leading-5 text-gray-600">{{ payroll.year }}</div>
                                </Link>
                            </td>
                            <td class="whitespace-no-wrap border-b border-gray-200">
                                <Link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                    <div class="px-6 pt-4 text-sm leading-5 text-gray-900">
                                        {{ payroll.created_by }}
                                    </div>
                                    <div class="px-6 pb-4 text-sm leading-5 text-gray-600">
                                        {{ payroll.date_created }}
                                    </div>
                                </Link>
                            </td>

                            <td class="w-px whitespace-no-wrap border-b border-gray-200">
                                <Link v-if="payroll.is_current && payroll.can_add_leave"
                                      href="#"
                                      @click.prevent="addLeaveAllowance(payroll.id)"
                                      class="btn-white" preserve-state>
                                    Add Annual Leave Allowance
                                </Link>
                            </td>
                            <td class="w-px whitespace-no-wrap border-b border-gray-200">
                                <Link href="#" @click.prevent="showModal(payroll.id)"preserve-state
                                      class="btn-white" >
                                    Add Schedule
                                </Link>
                            </td>

                            <td class="w-px whitespace-no-wrap border-b border-gray-200 text-sm leading-5 font-medium">
                                <Link href="#" @click="show(payroll.id)" class="px-6" preserve-state preserve-scroll>
                                    <icon name="cheveron-right" class="block w-6 h-4 fill-gray-400"/>
                                </Link>
                            </td>
                        </tr>

                        <tr v-if="show_detail[payroll.id]">
                            <td colspan="6"
                                class="whitespace-no-wrap text-left border-b border-gray-200 text-sm text-indigo-800 leading-5 font-medium">
                                <table class="min-w-full">
                                    <tr v-for="category in payroll.categories" :key="category.id">
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            {{ category.payment_title }}
                                            <span class="px-2 text-xs leading-5 font-semibold rounded-full"
                                                  :class="category.payment_type_id === 'sal' ? 'bg-green-100 text-green-800' : 'bg-pink-100 text-pink-800'">
                                             {{ category.payment_type }}
                                           </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            Total Amount: <span class="line-through">N</span>
                                            <span class="font-bold">
                                                {{ category.total_amount }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            Head Count:
                                            <span class="font-bold">
                                                {{ category.head_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            <Link
                                                :href="route('audit_mda_schedules.index', {audit_payroll_category: category.id})"
                                                class="px-5 py-3" preserve-state preserve-scroll>
                                                View Mdas
                                            </Link>
                                        </td>
                                    </tr>

                                    <tr v-for="category in payroll.other_categories" :key="category.id">
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            <div class="text-sm leading-5 text-gray-900">
                                                {{ category.payment_title }}
                                                <span class="px-2 text-xs leading-5 font-semibold rounded-full" :class="category.color">
                                                    {{ category.payment_type }}
                                                </span>
                                            </div>
                                            <div class="text-sm leading-5 ">
                                                <span v-if="!category.tenece && !category.fidelity" class="italic text-green-900">No Charge Applied</span>
                                                <span v-if="category.tenece && category.fidelity" class="italic text-pink-900">All Charges Applied</span>
                                                <span v-if="category.tenece && !category.fidelity" class="italic text-blue-900">Fidelity Charge not Applied</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            Total Amount: <span class="line-through">N</span>
                                            <span class="font-bold">
                                                {{ category.total_amount }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-left text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            Head Count:
                                            <span class="font-bold">
                                                {{ category.head_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-right text-sm border-b border-gray-100 bg-gray-200 leading-5 font-medium">
                                            <!--                                            :href="route('audit_mda_schedules.index', {audit_payroll_category: category.id})"-->
                                            <Link v-if="category.uploaded"
                                                  href="#"
                                                  class="px-5 py-3" preserve-state preserve-scroll>
                                                View Schedule
                                            </Link>

                                            <form v-else @submit.prevent="upload(category.id)" :key="category.id">
                                                <div class="flex items-center">
                                                    <file-input v-model="file.schedule_file[category.id]"
                                                                :errors="file.errors.schedule_file" class="pr-6 w-full" type="file"
                                                                accept="file/*"/>
                                                    <button type="submit"
                                                            class="px-4 py-1 h-1/2 bg-gray-600 hover:bg-gray-700 rounded-sm text-xs font-medium text-white focus:outline-none">
                                                        Upload
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </tbody>

                        <tbody>
                        <tr v-if="payrolls.data.length === 0">
                            <td colspan="3"
                                class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                No Payroll
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <pagination :links="payrolls.links" />
        <div v-if="showCreateModal" class="fixed z-10 inset-0 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeModal"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Create Other Schedule
                        </h3>
                        <div class="mt-5">
                            <select-input v-model="form.payment_type_id" :errors="form.errors.payment_type_id"
                                          class="pr-6 pb-6 w-full " label="Payment Type" required>
                                <option disabled value="" class="text-gray-100">Select Payment Type</option>
                                <option v-for="payment_type in payment_types" :key="payment_type.id" :value="payment_type.id">
                                    {{ payment_type.name }}
                                </option>
                            </select-input>

                            <text-input v-model="form.payment_title"
                                        :errors="form.errors.payment_title" class="pr-6 pb-6 w-full uppercase"
                                        label="Payment Title"
                                        required />

                            <fieldset class="flex justify-between">
                                <legend class="sr-only">Pay Commission Charges</legend>
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="form.paycomm_tenece" id="paycomm_tenece" name="paycomm_tenece" aria-describedby="paycomm-tenece-description" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="paycomm_tenece" class="font-medium text-gray-700">Apply Charges</label>
                                        <p id="paycomm-tenece-description" class="text-gray-500">Adds Paycomm II Line Item</p>
                                    </div>
                                </div>
                                <div v-if="form.paycomm_tenece"  class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input v-model="form.paycomm_fidelity" id="paycomm_fidelity" name="paycomm_fidelity" aria-describedby="paycomm_fidelity-description" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="paycomm_fidelity" class="font-medium text-gray-700">Apply Fidelity Charges</label>
                                        <p id="paycomm_fidelity-description" class="text-gray-500">Adds Paycomm I Line Item</p>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" @click="saveSchedule"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-8 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save
                </button>
                <button type="button" @click="closeModal"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-8 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import { Link, useForm } from '@inertiajs/vue3'
import TextInput from '@/Shared/TextInput'
import FileInput from '@/Shared/FileInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
    layout: Layout,

    props: {
        errors: Object,
        payrolls: Object,
        payment_types: Array,
    },

    components: {
        Icon,
        Link,
        TextInput,
        FileInput,
        Pagination,
        SelectInput,
        LoadingButton,
    },

    setup() {
        const form = useForm({
            payment_type_id: null,
            payment_title: null,
            paycomm_tenece: false,
            paycomm_fidelity: false,
        })
        const file = useForm({
            schedule_file: [],
        })
        return { form, file }
    },

    data() {
        return {
            show_detail: [],
            payroll_id: null,
            showCreateModal: false,
        }
    },

    methods: {
        show(payroll) {
            this.show_detail[payroll] = !this.show_detail[payroll]
        },

        saveSchedule() {
            this.form
                .transform((data) => ({
                    ...data,
                    audit_payroll_id: this.payroll_id,
                }))
                .post(this.route('other_audit_payroll.store'), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal()
                })
        },

        upload(category_id) {
            this.file
                .transform((data) => ({
                    other_audit_payroll_category_id: category_id,
                    schedule_file: data.schedule_file[category_id]
                }))
                .post('/other_audit_schedule/store', {
                    preserveScroll: true
                })
        },

        showModal(payroll_id) {
            this.payroll_id = payroll_id
            this.showCreateModal = true
        },

        closeModal() {
            this.showCreateModal = false
            this.form.reset()
        },

        addLeaveAllowance(payroll_id) {
            let result = confirm('Are you sure you want to add Annual Leave Allowance')

            if (result) {
                this.$inertia.get(route('audit_payroll.leave', {audit_payroll: payroll_id}))
            }
        }
    },
}
</script>
