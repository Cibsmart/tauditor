<template>
  <div>
    <Head title="MDAs" />
    <h1 class="mb-8 text-3xl font-bold">MDAs</h1>

    <div>
      <div v-if="can.create_mda" class="mb-6 flex items-center justify-between">
        <div></div>
        <Button size="lg" @click="openModal">
          Add<span class="hidden md:inline">&nbsp; MDA</span>
        </Button>
      </div>

      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Code</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Has Sub-MDAs</TableHead>
              <TableHead>Sub-MDAs</TableHead>
              <TableHead>Status</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="mda in mdas.data" :key="mda.id">
              <TableCell>
                <div class="text-sm leading-5 font-medium uppercase">
                  {{ mda.code }}
                </div>
              </TableCell>
              <TableCell>
                <div class="text-sm leading-5 font-medium uppercase">
                  {{ mda.name }}
                </div>
              </TableCell>
              <TableCell>
                <span
                  :class="
                    mda.has_sub
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-800'
                  "
                  class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold"
                >
                  {{ mda.has_sub ? 'Yes' : 'No' }}
                </span>
              </TableCell>
              <TableCell>
                <div class="text-sm leading-5 font-light">
                  {{ mda.subs_count }}
                </div>
              </TableCell>
              <TableCell>
                <span
                  :class="
                    mda.active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  "
                  class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold"
                >
                  {{ mda.active ? 'Active' : 'Inactive' }}
                </span>
              </TableCell>
            </TableRow>
            <TableRow v-if="mdas.data && mdas.data.length === 0">
              <TableCell
                class="text-xs font-medium tracking-wider text-gray-700 uppercase"
                colspan="5"
              >
                No MDA Found
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <pagination :links="mdas.links" />
    </div>

    <Dialog
      :open="showCreateModal"
      @update:open="(open) => !open && closeModal()"
    >
      <DialogContent class="max-w-2xl">
        <DialogHeader>
          <DialogTitle>Create MDA</DialogTitle>
        </DialogHeader>

        <div class="space-y-4">
          <text-input
            v-model="form.code"
            :errors="form.errors.code"
            class="w-full uppercase"
            label="MDA Code"
            required
          />

          <text-input
            v-model="form.name"
            :errors="form.errors.name"
            class="w-full"
            label="MDA Name"
            required
          />

          <select-input
            v-model="form.beneficiary_type_id"
            :errors="form.errors.beneficiary_type_id"
            class="w-full"
            label="Beneficiary Type"
            required
          >
            <option disabled value="">Select Beneficiary Type</option>
            <option
              v-for="type in beneficiaryTypes"
              :key="type.id"
              :value="type.id"
            >
              {{ type.name }}
            </option>
          </select-input>

          <check-input v-model="form.has_sub" label="This MDA has Sub-MDAs" />

          <div v-if="form.has_sub">
            <label class="mb-2 block select-none">Sub-MDAs</label>
            <div
              v-for="(sub, index) in form.sub_mdas"
              :key="index"
              class="mb-3 flex items-start gap-3"
            >
              <text-input
                v-model="form.sub_mdas[index]"
                :errors="form.errors[`sub_mdas.${index}`]"
                :label="null"
                class="flex-1"
              />
              <Button
                size="sm"
                type="button"
                variant="outline"
                @click="removeSubMda(index)"
              >
                Remove
              </Button>
            </div>
            <div v-if="form.errors.sub_mdas" class="mb-3 text-sm text-red-800">
              {{ form.errors.sub_mdas }}
            </div>
            <Button type="button" variant="outline" @click="addSubMda">
              Add Sub-MDA
            </Button>
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="closeModal">
            Cancel
          </Button>
          <Button :disabled="form.processing" type="button" @click="saveMda">
            <Spinner v-if="form.processing" class="mr-2" />
            Save
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/Components/ui/dialog';
import { Spinner } from '@/Components/ui/spinner';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table';
import CheckInput from '@/Shared/CheckInput';
import Layout from '@/Shared/Layout';
import Pagination from '@/Shared/Pagination';
import SelectInput from '@/Shared/SelectInput';
import TextInput from '@/Shared/TextInput';

export default {
  layout: Layout,

  props: {
    can: Object,
    mdas: Object,
    beneficiaryTypes: Array,
  },

  components: {
    Pagination,
    Button,
    CheckInput,
    SelectInput,
    TextInput,
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    Spinner,
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
  },

  setup() {
    const form = useForm({
      code: null,
      name: null,
      beneficiary_type_id: null,
      has_sub: false,
      sub_mdas: [],
    });

    return { form };
  },

  data() {
    return {
      showCreateModal: false,
    };
  },

  watch: {
    'form.has_sub'(hasSub) {
      if (hasSub && this.form.sub_mdas.length === 0) {
        this.form.sub_mdas.push('');
      }

      if (!hasSub) {
        this.form.sub_mdas = [];
      }
    },
  },

  methods: {
    openModal() {
      this.showCreateModal = true;
    },

    closeModal() {
      this.showCreateModal = false;
      this.form.reset();
      this.form.clearErrors();
    },

    addSubMda() {
      this.form.sub_mdas.push('');
    },

    removeSubMda(index) {
      this.form.sub_mdas.splice(index, 1);
    },

    saveMda() {
      this.form.post(this.route('mdas.store'), {
        preserveScroll: true,
        onSuccess: () => this.closeModal(),
      });
    },
  },
};
</script>
