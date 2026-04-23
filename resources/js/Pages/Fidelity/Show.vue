<template>
    <div>
        <Head title="Show Mandate Detail" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link :href="route('fidelity.index')">Loan Mandates </Link>
            <span class="font-medium">/</span> Detail
        </h1>
        <div class="max-w-3xl overflow-hidden rounded shadow">
            <form @submit.prevent="form.post(route('fidelity.store'))">
                <div class="-mr-6 -mb-8 flex flex-wrap p-8">
                    <label-input
                        v-model="mandatee.reference"
                        class="w-full pr-6 pb-8"
                        label="Mandate Reference"
                    >
                        <span
                            :class="mandatee.color"
                            class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold lowercase"
                        >
                            {{ mandatee.status }}
                        </span>

                        <span
                            v-if="!mandate.processed"
                            class="inline-flex rounded-full bg-red-100 px-2 text-xs leading-5 font-semibold text-red-800 lowercase"
                        >
                            pending
                        </span>
                    </label-input>
                    <label-input
                        v-model="mandatee.name"
                        class="w-full pr-6 pb-8"
                        label="Beneficiary"
                    />
                    <label-input
                        v-model="mandatee.verification_number"
                        class="w-full pr-6 pb-8"
                        label="Staff ID"
                    />
                    <label-input
                        v-model="mandatee.bvn"
                        class="w-full pr-6 pb-8"
                        label="Bank Verification Number"
                    />
                    <label-input
                        v-model="mandatee.account_number"
                        class="w-full pr-6 pb-8"
                        label="Account Number"
                    />
                    <label-input
                        v-model="mandatee.loan_amount"
                        class="w-full pr-6 pb-8"
                        label="Loan Amount"
                    />
                    <label-input
                        v-model="mandatee.collection_amount"
                        class="w-full pr-6 pb-8"
                        label="Collection Amount"
                    />
                    <label-input
                        v-model="mandatee.total_collection_amount"
                        class="w-full pr-6 pb-8"
                        label="Total Collection Amount"
                    />
                    <label-input
                        v-model="mandatee.number_of_repayments"
                        class="w-full pr-6 pb-8"
                        label="Number of Repayments"
                    />
                    <label-input
                        v-model="mandatee.number_repaid"
                        class="w-full pr-6 pb-8"
                        label="Number of Times Repaid"
                    />
                    <label-input
                        v-model="mandatee.date_disbursed"
                        class="w-full pr-6 pb-8"
                        label="Disbursement Date"
                    />
                </div>
                <div class="flex items-center justify-end border-t px-8 py-4">
                    <Button
                        :as="Link"
                        :href="route('fidelity.index')"
                        class="mr-5"
                        variant="secondary"
                        >Back</Button
                    >
                    <loading-button
                        v-if="!mandatee.processed"
                        :loading="form.processing"
                        type="submit"
                    >
                        Processed
                    </loading-button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import LabelInput from '@/Shared/LabelInputInline';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';

export default {
    layout: Layout,

    components: {
        Link,
        LabelInput,
        LoadingButton,
        Button,
    },

    props: {
        mandate: Object,
        domain: String,
    },

    setup(props) {
        const form = useForm({
            mandate_id: props.mandate.id,
        });

        return { form };
    },

    data() {
        return {
            sending: false,
            mandatee: this.mandate,
        };
    },
};
</script>
