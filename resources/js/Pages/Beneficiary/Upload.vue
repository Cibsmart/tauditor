<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Upload Beneficiaries</h1>

        <div class="flex flex-col">
            <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                    <form @submit.prevent="upload()">
                        <div class="flex items-center">
                            <file-input v-model="form.file" :errors="form.errors.file" class="pr-6 w-full" type="file" accept="file/*" />
                            <button type="submit" class="px-4 py-1 h-1/2 bg-gray-600 hover:bg-gray-700 rounded-sm text-xs font-medium text-white focus:outline-none">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import FileInput from "@/Shared/FileInput";
import Pagination from '@/Shared/Pagination'

export default {
    metaInfo: { title: 'Upload Beneficiaries' },
    layout: Layout,

    components: {
        Icon,
        FileInput,
        Pagination,
    },

    data(){
        return {
            form: this.$inertia.form({
                file: null,
            })
        }
    },

    methods: {
        upload(){
            this.form.post(this.route('beneficiaries.upload'))
        }
    }
}
</script>
