<template>
    <div>
        <Head title="Beneficiaries" />
        <h1 class="mb-8 font-bold text-3xl">Beneficiaries</h1>
        <div class="mb-6 flex justify-between items-center">
            <!-- Search Filter goes here -->
            <search-filter v-model="form.search" class="w-full max-w-lg mr-4">
            </search-filter>
            <div></div>
            <Button :as="Link" href="#" size="lg">
                <span class="hidden md:inline">New Beneficiary</span>
            </Button>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>MDA</TableHead>
                        <TableHead>Designation</TableHead>
                        <TableHead>Bank Details</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(beneficiary, index) in beneficiaries.data" :key="beneficiary.id">
                        <TableCell>
                            <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{
                                    beneficiary.name
                                }}
                            </div>
                            <div class="text-sm leading-5 text-gray-600">{{ beneficiary.verification_number }}</div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">{{ beneficiary.mda }}</div>
                            <div class="text-sm leading-5 text-gray-600">
                                {{ beneficiary.sub_mda }}
                                {{ beneficiary.sub_sub_mda }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">{{ beneficiary.designation }}</div>
                            <div class="text-sm leading-5 text-gray-600">
                                {{ beneficiary.step }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="text-sm leading-5 text-gray-900">
                                {{ beneficiary.account_number }}
                            </div>
                            <div class="text-sm leading-5 text-gray-600">
                                {{ beneficiary.bank_name }}
                            </div>
                        </TableCell>
                        <TableCell>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                  :class="beneficiary.active
                                  ? 'bg-green-100 text-green-800'
                                  : 'bg-red-100 text-red-800'">
                                {{ beneficiary.active ? 'Active' : 'Inactive' }}
                            </span>
                        </TableCell>
                        <TableCell class="text-right">
                            <a href="#"
                               class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="beneficiaries.data.length === 0">
                        <TableCell colspan="6" class="text-xs font-medium text-gray-700 uppercase tracking-wider">
                            No Beneficiary
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
        <pagination :links="beneficiaries.links"/>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import {Link} from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table'

export default {
    layout: Layout,

    props: {
        beneficiaries: Object,
        filters: Object,
    },

    components: {
        Icon,
        Link,
        Pagination,
        SearchFilter,
        Button,
        Table,
        TableHeader,
        TableBody,
        TableRow,
        TableHead,
        TableCell,
    },

    data() {
        return {
            form: {
                search: this.filters.search,
            },
        }
    },

    watch: {
        form: {
            handler: throttle(function () {
                let query = pickBy(this.form)
                this.$inertia.visit(this.route('beneficiaries.index', Object.keys(query).length ? query : {remember: 'forget'}), { replace: true })
            }, 300),
            deep: true,
        },
    },
}
</script>
