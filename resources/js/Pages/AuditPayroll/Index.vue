<template>
    <div>
        <Head title="Audit Payroll" />
        <h1 class="mb-8 font-bold text-3xl">Audit Payrolls</h1>
        <div class="mb-6 flex justify-between items-center">
            <div></div>
            <Button :as="Link" :href="route('audit_payroll.store')" method="post" size="lg" preserve-state>
                Add <span class="hidden md:inline"> &nbsp; Payroll</span>
            </Button>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Month</TableHead>
                        <TableHead>Created By</TableHead>
                        <TableHead>&nbsp;</TableHead>
                        <TableHead>&nbsp;</TableHead>
                        <TableHead class="text-right">Details</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody v-for="payroll in payrolls.data" :key="payroll.id">
                    <TableRow :key="payroll.id">
                        <TableCell>
                            <Link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                <div class="pt-0 text-sm leading-5 font-medium text-gray-900 uppercase">
                                    {{ payroll.month }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">{{ payroll.year }}</div>
                            </Link>
                        </TableCell>
                        <TableCell>
                            <Link href="#" @click="show(payroll.id)" class="" preserve-state preserve-scroll>
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ payroll.created_by }}
                                </div>
                                <div class="text-sm leading-5 text-gray-600">
                                    {{ payroll.date_created }}
                                </div>
                            </Link>
                        </TableCell>

                        <TableCell class="w-px">
                            <Button v-if="payroll.is_current && payroll.can_add_leave" :as="Link"
                                  href="#"
                                  variant="outline" size="sm"
                                  @click.prevent="addLeaveAllowance(payroll.id)"
                                  preserve-state>
                                Add Annual Leave Allowance
                            </Button>
                        </TableCell>
                        <TableCell class="w-px">
                            <Button :as="Link" href="#" @click.prevent="showModal(payroll.id)" preserve-state
                                  variant="outline" size="sm">
                                Add Schedule
                            </Button>
                        </TableCell>

                        <TableCell class="w-px text-sm leading-5 font-medium">
                            <Link href="#" @click="show(payroll.id)" class="px-6" preserve-state preserve-scroll>
                                <icon name="cheveron-right" class="block w-6 h-4 fill-gray-400"/>
                            </Link>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="show_detail[payroll.id]">
                        <TableCell colspan="6">
                            <Table>
                                <TableBody>
                                    <TableRow v-for="category in payroll.categories" :key="category.id">
                                        <TableCell class="bg-gray-200">
                                            {{ category.payment_title }}
                                            <span class="px-2 text-xs leading-5 font-semibold rounded-full"
                                                  :class="category.payment_type_id === 'sal' ? 'bg-green-100 text-green-800' : 'bg-pink-100 text-pink-800'">
                                                {{ category.payment_type }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            Total Amount: <span class="line-through">N</span>
                                            <span class="font-bold">
                                                {{ category.total_amount }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            Head Count:
                                            <span class="font-bold">
                                                {{ category.head_count }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="text-right bg-gray-200">
                                            <Link
                                                :href="route('audit_mda_schedules.index', {audit_payroll_category: category.id})"
                                                class="px-5 py-3" preserve-state preserve-scroll>
                                                View Mdas
                                            </Link>
                                        </TableCell>
                                    </TableRow>

                                    <TableRow v-for="category in payroll.other_categories" :key="category.id">
                                        <TableCell class="bg-gray-200">
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
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            Total Amount: <span class="line-through">N</span>
                                            <span class="font-bold">
                                                {{ category.total_amount }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="bg-gray-200">
                                            Head Count:
                                            <span class="font-bold">
                                                {{ category.head_count }}
                                            </span>
                                        </TableCell>
                                        <TableCell class="text-right bg-gray-200">
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
                                                    <Button type="submit" variant="secondary" size="sm">
                                                        Upload
                                                    </Button>
                                                </div>
                                            </form>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </TableCell>
                    </TableRow>
                </TableBody>

                <TableBody>
                    <TableRow v-if="payrolls.data.length === 0">
                        <TableCell colspan="3" class="text-xs font-medium text-gray-700 uppercase tracking-wider">
                            No Payroll
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
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
                <Button type="button" @click="saveSchedule" class="sm:ml-3">
                    Save
                </Button>
                <Button type="button" variant="outline" @click="closeModal" class="sm:ml-3">
                    Cancel
                </Button>
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
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

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
        Button,
        Table,
        TableHeader,
        TableBody,
        TableRow,
        TableHead,
        TableCell,
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
