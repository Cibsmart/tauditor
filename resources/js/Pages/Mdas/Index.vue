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
              <TableHead v-if="can.create_mda" class="text-right">
                Actions
              </TableHead>
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
              <TableCell v-if="can.create_mda" class="text-right">
                <div class="flex justify-end gap-2">
                  <Button
                    size="sm"
                    variant="outline"
                    @click="openEditModal(mda)"
                  >
                    Edit
                  </Button>
                  <Button
                    size="sm"
                    variant="outline"
                    @click="openSubsModal(mda)"
                  >
                    Add Sub-MDAs
                  </Button>
                  <Button
                    :variant="mda.active ? 'destructive' : 'secondary'"
                    size="sm"
                    @click="toggleActive(mda)"
                  >
                    {{ mda.active ? 'Deactivate' : 'Activate' }}
                  </Button>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="mdas.data && mdas.data.length === 0">
              <TableCell
                :colspan="can.create_mda ? 6 : 5"
                class="text-xs font-medium tracking-wider uppercase"
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
            class="w-full uppercase"
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

    <Dialog
      :open="showEditModal"
      @update:open="(open) => !open && closeEditModal()"
    >
      <DialogContent class="max-w-2xl">
        <DialogHeader>
          <DialogTitle>Edit MDA</DialogTitle>
        </DialogHeader>

        <div class="space-y-4">
          <text-input
            :model-value="selectedCode"
            class="w-full uppercase"
            disabled
            label="MDA Code"
          />

          <text-input
            v-model="editForm.name"
            :errors="editForm.errors.name"
            class="w-full uppercase"
            label="MDA Name"
            required
          />
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="closeEditModal">
            Cancel
          </Button>
          <Button
            :disabled="editForm.processing"
            type="button"
            @click="updateMda"
          >
            <Spinner v-if="editForm.processing" class="mr-2" />
            Save
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <Dialog
      :open="showSubsModal"
      @update:open="(open) => !open && closeSubsModal()"
    >
      <DialogContent class="max-w-2xl">
        <DialogHeader>
          <DialogTitle>Add Sub-MDAs to {{ selectedCode }}</DialogTitle>
        </DialogHeader>

        <div class="space-y-4">
          <div v-if="selectedExistingSubs.length">
            <label class="mb-2 block select-none">Existing Sub-MDAs</label>
            <ul class="list-inside list-disc text-sm uppercase">
              <li v-for="sub in selectedExistingSubs" :key="sub.id">
                {{ sub.name }}
              </li>
            </ul>
          </div>

          <div>
            <label class="mb-2 block select-none">New Sub-MDAs</label>
            <div
              v-for="(sub, index) in subsForm.sub_mdas"
              :key="index"
              class="mb-3 flex items-start gap-3"
            >
              <text-input
                v-model="subsForm.sub_mdas[index]"
                :errors="subsForm.errors[`sub_mdas.${index}`]"
                :label="null"
                class="flex-1"
              />
              <Button
                size="sm"
                type="button"
                variant="outline"
                @click="removeNewSub(index)"
              >
                Remove
              </Button>
            </div>
            <div
              v-if="subsForm.errors.sub_mdas"
              class="mb-3 text-sm text-red-800"
            >
              {{ subsForm.errors.sub_mdas }}
            </div>
            <Button type="button" variant="outline" @click="addNewSub">
              Add Sub-MDA
            </Button>
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="closeSubsModal">
            Cancel
          </Button>
          <Button
            :disabled="subsForm.processing"
            type="button"
            @click="saveSubs"
          >
            <Spinner v-if="subsForm.processing" class="mr-2" />
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

    const editForm = useForm({
      name: null,
    });

    const subsForm = useForm({
      sub_mdas: [],
    });

    return { form, editForm, subsForm };
  },

  data() {
    return {
      showCreateModal: false,
      showEditModal: false,
      showSubsModal: false,
      selectedMdaId: null,
      selectedCode: '',
      selectedExistingSubs: [],
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

    openEditModal(mda) {
      this.selectedMdaId = mda.id;
      this.selectedCode = mda.code;
      this.editForm.clearErrors();
      this.editForm.name = mda.name;
      this.showEditModal = true;
    },

    closeEditModal() {
      this.showEditModal = false;
      this.editForm.reset();
      this.editForm.clearErrors();
    },

    updateMda() {
      this.editForm.patch(
        this.route('mdas.update', { mda: this.selectedMdaId }),
        {
          preserveScroll: true,
          onSuccess: () => this.closeEditModal(),
        },
      );
    },

    openSubsModal(mda) {
      this.selectedMdaId = mda.id;
      this.selectedCode = mda.code;
      this.selectedExistingSubs = mda.subs || [];
      this.subsForm.reset();
      this.subsForm.clearErrors();
      this.subsForm.sub_mdas = [''];
      this.showSubsModal = true;
    },

    closeSubsModal() {
      this.showSubsModal = false;
      this.subsForm.reset();
      this.subsForm.clearErrors();
    },

    addNewSub() {
      this.subsForm.sub_mdas.push('');
    },

    removeNewSub(index) {
      this.subsForm.sub_mdas.splice(index, 1);
    },

    saveSubs() {
      this.subsForm.post(
        this.route('mdas.subs.store', { mda: this.selectedMdaId }),
        {
          preserveScroll: true,
          onSuccess: () => this.closeSubsModal(),
        },
      );
    },

    toggleActive(mda) {
      const action = mda.active ? 'deactivate' : 'activate';

      if (!confirm(`Are you sure you want to ${action} ${mda.code}?`)) {
        return;
      }

      this.$inertia.post(
        this.route('mdas.toggle_active', { mda: mda.id }),
        {},
        { preserveScroll: true },
      );
    },
  },
};
</script>
