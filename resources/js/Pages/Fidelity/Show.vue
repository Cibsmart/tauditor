<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('fidelity.index')">Loan Mandates
            </inertia-link>
            <span class="text-indigo-400 font-medium">/</span> Detail
        </h1>
        <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
            <form @submit.prevent="form.post(route('fidelity.store'))">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <label-input v-model="mandatee.reference" class="pr-6 pb-8 w-full" label="Mandate Reference">
                        <span class="px-2 inline-flex text-xs lowercase leading-5 font-semibold rounded-full"
                              :class="mandatee.color">
                            {{ mandatee.status }}
                        </span>

                        <span v-if="! mandate.processed" class="px-2 inline-flex text-xs lowercase leading-5 font-semibold bg-red-100 text-red-800 rounded-full">
                            pending
                        </span>
                    </label-input>
                    <label-input v-model="mandatee.name" class="pr-6 pb-8 w-full" label="Beneficiary"/>
                    <label-input v-model="mandatee.verification_number" class="pr-6 pb-8 w-full" label="Staff ID"/>
                    <label-input v-model="mandatee.bvn" class="pr-6 pb-8 w-full" label="Bank Verification Number"/>
                    <label-input v-model="mandatee.account_number" class="pr-6 pb-8 w-full" label="Account Number"/>
                    <label-input v-model="mandatee.loan_amount" class="pr-6 pb-8 w-full" label="Loan Amount"/>
                    <label-input v-model="mandatee.collection_amount" class="pr-6 pb-8 w-full" label="Collection Amount"/>
                    <label-input v-model="mandatee.total_collection_amount" class="pr-6 pb-8 w-full" label="Total Collection Amount"/>
                    <label-input v-model="mandatee.number_of_repayments" class="pr-6 pb-8 w-full" label="Number of Repayments"/>
                    <label-input v-model="mandatee.number_repaid" class="pr-6 pb-8 w-full" label="Number of Times Repaid"/>
                    <label-input v-model="mandatee.date_disbursed" class="pr-6 pb-8 w-full" label="Disbursement Date"/>
                </div>
                <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex justify-end items-center">
                    <Link :href="route('fidelity.index')" class="mr-5 inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Back</Link>
                    <loading-button v-if="! mandatee.processed" :loading="form.processing" class="btn-indigo" type="submit">Processed</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>


<script>
import Layout from '@/Shared/Layout';
import { Link } from '@inertiajs/inertia-vue';
import LabelInput from '@/Shared/LabelInputInline';
import LoadingButton from '@/Shared/LoadingButton';

export default {
    metaInfo: {title: 'Show Mandate Detail'},

    layout: Layout,

    components: {
        Link,
        LabelInput,
        LoadingButton,
    },

    props: {
        mandate: Object,
        domain: String,
    },

    data() {
        return {
            sending: false,
            mandatee: this.mandate,
            form: this.$inertia.form({
                mandate_id: this.mandate.id,
            }),
        }
    },
}
</script>

