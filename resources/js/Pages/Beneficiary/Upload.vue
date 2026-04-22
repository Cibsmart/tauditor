<template>
  <div>
    <Head title="Upload Beneficiaries" />
    <h1 class="mb-8 font-bold text-3xl">Upload Beneficiaries</h1>

    <div class="flex flex-col">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b ">
          <form @submit.prevent="upload()">
            <div class="flex items-center">
              <file-input v-model="form.file" :errors="form.errors.file" accept="file/*" class="pr-6 w-full"
                          type="file" />
              <Button type="submit">
                Upload
              </Button>
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
import { useForm } from '@inertiajs/vue3'
import { Button } from "@/Components/ui/button";

export default {
  layout: Layout,

  components: {
    Button,
    Icon,
    FileInput,
    Pagination,
  },

  setup() {
    const form = useForm({
      file: null,
    })
    return { form }
  },

  methods: {
    upload() {
      this.form.post(this.route('beneficiaries.upload'))
    },
  },
}
</script>
