<template>
    <div class="container p-8 bg-white">

        <div class="mb-6 flex justify-between items-center">
            <h1 class="mb-8 font-bold text-3xl">Deductions</h1>
            <inertia-link href="#"
                          class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap ">
                <span class="hidden md:inline">New Deduction</span>
            </inertia-link>
        </div>

        <hr/>

        <form @submit.prevent="submit" class="container p-8">
            <div class="grid row-gap-6 grid-cols-5">
                <div class="col-start-1 col-end-2">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Assign To
                    </label>
                    <select-input v-model="form.deductible_type">
                        <option disabled value="">Select</option>
                        <option value="all">Every One</option>
                        <option value="beneficiary_type">Beneficiary Type</option>
                        <option value="salary_structure">Salary Structure</option>
                        <option value="cadre">Cadre</option>
                        <option value="cadre_step">Cadre Step</option>
                        <option value="mda_structure">Mda Structure</option>
                    </select-input>
                    <div class="text-red-600" v-if="$page.errors.deductible_type">{{ $page.errors.deductible_type[0]
                        }}
                    </div>
                </div>
                <div class="col-start-3 col-end-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Start Date
                    </label>
                    <text-input type="date" placeholder="Start Date" v-model="form.start_date"></text-input>
                    <div class="text-red-600" v-if="$page.errors.start_date">{{ $page.errors.start_date[0] }}</div>
                </div>
                <div class="col-end-6 col-span-1">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        End Date
                    </label>
                    <text-input type="date" placeholder="End Date" v-model="form.end_date"></text-input>
                    <div class="text-red-600" v-if="$page.errors.end_date">{{ $page.errors.end_date[0] }}</div>
                </div>

                <div class="col-start-1 col-end-3" v-show="form.deductible_type === 'beneficiary_type'">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Beneficiary Type
                    </label>
                    <select-input v-model="form.beneficiary_type">
                        <option disabled value="">Select Beneficiary Type</option>
                        <option
                            v-for="(beneficiary_type, index) in beneficiary_types"
                            :key="beneficiary_type.id"
                            :value="beneficiary_type.id">
                            {{ beneficiary_type.name }}
                        </option>
                    </select-input>
                    <div class="text-red-600" v-if="$page.errors.beneficiary_type">{{ $page.errors.beneficiary_type[0]
                        }}
                    </div>
                </div>

                <div class="col-start-1 col-end-3"
                     v-show="form.deductible_type === 'salary_structure' || form.deductible_type === 'mda_structure'">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Salary Structure
                    </label>
                    <select-input v-model="form.salary_structure">
                        <option disabled value="">Select Salary Structure</option>
                        <option
                            v-for="(salary_structure, index) in salary_structures"
                            :key="salary_structure.id"
                            :value="salary_structure.id">
                            {{ salary_structure.name }}
                        </option>
                    </select-input>
                    <div class="text-red-600" v-if="$page.errors.salary_structure">{{ $page.errors.salary_structure[0]
                        }}
                    </div>
                </div>
                <div class="col-end-6 col-span-2" v-show="form.deductible_type === 'mda_structure'">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Mda
                    </label>
                    <select-input v-model="form.mda">
                        <option disabled value="">Select Mda</option>
                        <option
                            v-for="(mda, index) in mdas"
                            :key="mda.id"
                            :value="mda.id">
                            {{ mda.name }}
                        </option>
                    </select-input>
                    <div class="text-red-600" v-if="$page.errors.mda">{{ $page.errors.mda[0] }}</div>
                </div>

                <div class="col-start-1 col-end-3"
                     v-show="form.deductible_type === 'cadre' || form.deductible_type === 'cadre_step'">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Cadre
                    </label>
                    <select-input @input="cadreChange" v-model="form.cadre">
                        <option disabled value="">Select Cadre</option>
                        <option
                            v-for="(cadre, index) in cadres"
                            :key="cadre.id"
                            :value="cadre.id">
                            {{'Grade Level ' + cadre.grade_level_id}}
                        </option>
                    </select-input>
                    <div class="text-red-600" v-if="$page.errors.cadre">{{ $page.errors.cadre[0] }}</div>
                </div>

                <div class="col-start-1 col-end-3" v-show="form.deductible_type === 'cadre_step'">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Cadre Step
                    </label>
                    <select-input v-model="form.cadre_step">
                        <option disabled value="">Select Cadre Step</option>
                        <option
                            v-for="(cadre_step, index) in cadre_steps_data"
                            :key="cadre_step.id"
                            :value="cadre_step.id">
                            {{'Sept '+ cadre_step.step_id }}
                        </option>
                    </select-input>
                    <div class="text-red-600" v-if="$page.errors.cadre_step">{{ $page.errors.cadre_step[0] }}</div>
                </div>

                <div class="col-start-1">
                    <button
                        class="@apply px-6 py-3 flex items-center rounded bg-indigo-800 text-white text-sm font-bold whitespace-no-wrap"
                        type="submit">
                        Save
                    </button>
                </div>
            </div>
        </form>

    </div>
</template>

<script>
    import Icon from '@/Shared/Icon'
    import Layout from '@/Shared/Layout'
    import Pagination from '@/Shared/Pagination'
    import SearchFilter from '@/Shared/SearchFilter'
    import TextInput from '@/Shared/TextInput'
    import SelectInput from '@/Shared/SelectInput'

    export default {
        metaInfo: {title: 'Deductions'},
        layout: Layout,

        props: {
            beneficiary_types: Array,
            salary_structures: Array,
            mdas: Array,
            cadres: Array,
            cadre_steps: Array,
            deduction_id: Number,
        },

        components: {
            Icon,
            Pagination,
            SearchFilter,
            TextInput,
            SelectInput,
        },

        data() {
            return {
                form: {
                    deductible_type: '',
                    beneficiary_type: '',
                    salary_structure: '',
                    mda: '',
                    cadre: '',
                    cadre_step: '',
                    start_date: '',
                    end_date: '',
                    deduction_idd: this.deduction_id,
                },
                cadre_steps_data: [],
            }
        },

        methods: {
            cadreChange(value) {
                this.cadre_steps_data = this.cadre_steps.filter(item => {
                    return item.cadre_id === value;
                })
            },
            submit() {
                this.$inertia.post(route('deductibles.store'), this.form)
            },
        },
    }
</script>
