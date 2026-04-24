<template>
  <div>
    <Head title="Audit Payroll" />
    <h1 class="mb-8 text-3xl font-bold">Audit Payrolls</h1>
    <div class="mb-6 flex items-center justify-between">
      <div></div>
      <Button asChild size="lg">
        <Link :href="route('audit_payroll.store')" method="post" preserve-state>
          Add <span class="hidden md:inline"> &nbsp; Payroll</span>
        </Link>
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
              <Link
                class=""
                href="#"
                preserve-scroll
                preserve-state
                @click="show(payroll.id)"
              >
                <div class="pt-0 text-sm leading-5 font-medium uppercase">
                  {{ payroll.month }}
                </div>
                <div class="text-sm leading-5 text-muted-foreground">
                  {{ payroll.year }}
                </div>
              </Link>
            </TableCell>
            <TableCell>
              <Link
                class=""
                href="#"
                preserve-scroll
                preserve-state
                @click="show(payroll.id)"
              >
                <div class="text-sm leading-5">
                  {{ payroll.created_by }}
                </div>
                <div class="text-sm leading-5 text-muted-foreground">
                  {{ payroll.date_created }}
                </div>
              </Link>
            </TableCell>

            <TableCell class="w-px">
              <Button
                v-if="payroll.is_current && payroll.can_add_leave"
                :as="Link"
                href="#"
                preserve-state
                size="sm"
                variant="outline"
                @click.prevent="addLeaveAllowance(payroll.id)"
              >
                Add Annual Leave Allowance
              </Button>
            </TableCell>
            <TableCell class="w-px">
              <Button
                :as="Link"
                href="#"
                preserve-state
                size="sm"
                variant="outline"
                @click.prevent="showModal(payroll.id)"
              >
                Add Schedule
              </Button>
            </TableCell>

            <TableCell class="w-px text-sm leading-5 font-medium">
              <Link
                class="px-6"
                href="#"
                preserve-scroll
                preserve-state
                @click="show(payroll.id)"
              >
                <icon
                  class="block h-4 w-6 fill-gray-400"
                  name="cheveron-right"
                />
              </Link>
            </TableCell>
          </TableRow>

          <TableRow v-if="show_detail[payroll.id]">
            <TableCell colspan="6">
              <Table>
                <TableBody>
                  <TableRow
                    v-for="category in payroll.categories"
                    :key="category.id"
                  >
                    <TableCell>
                      {{ category.payment_title }}
                      <span
                        :class="
                          category.payment_type_id === 'sal'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-pink-100 text-pink-800'
                        "
                        class="rounded-full px-2 text-xs leading-5 font-semibold"
                      >
                        {{ category.payment_type }}
                      </span>
                    </TableCell>
                    <TableCell>
                      Total Amount:
                      <span class="line-through">N</span>
                      <span class="font-bold">
                        {{ category.total_amount }}
                      </span>
                    </TableCell>
                    <TableCell>
                      Head Count:
                      <span class="font-bold">
                        {{ category.head_count }}
                      </span>
                    </TableCell>
                    <TableCell class="text-right">
                      <Link
                        :href="
                          route('audit_mda_schedules.index', {
                            audit_payroll_category: category.id,
                          })
                        "
                        class="px-5 py-3"
                        preserve-scroll
                        preserve-state
                      >
                        View Mdas
                      </Link>
                    </TableCell>
                  </TableRow>

                  <TableRow
                    v-for="category in payroll.other_categories"
                    :key="category.id"
                  >
                    <TableCell>
                      <div class="text-sm leading-5">
                        {{ category.payment_title }}
                        <span
                          :class="category.color"
                          class="rounded-full px-2 text-xs leading-5 font-semibold"
                        >
                          {{ category.payment_type }}
                        </span>
                      </div>
                      <div class="text-sm leading-5">
                        <span
                          v-if="!category.tenece && !category.fidelity"
                          class="text-green-900 italic"
                          >No Charge Applied</span
                        >
                        <span
                          v-if="category.tenece && category.fidelity"
                          class="text-pink-900 italic"
                          >All Charges Applied</span
                        >
                        <span
                          v-if="category.tenece && !category.fidelity"
                          class="text-blue-900 italic"
                          >Fidelity Charge not Applied</span
                        >
                      </div>
                    </TableCell>
                    <TableCell>
                      Total Amount:
                      <span class="line-through">N</span>
                      <span class="font-bold">
                        {{ category.total_amount }}
                      </span>
                    </TableCell>
                    <TableCell>
                      Head Count:
                      <span class="font-bold">
                        {{ category.head_count }}
                      </span>
                    </TableCell>
                    <TableCell class="text-right">
                      <Link
                        v-if="category.uploaded"
                        class="px-5 py-3"
                        href="#"
                        preserve-scroll
                        preserve-state
                      >
                        View Schedule
                      </Link>

                      <form
                        v-else
                        :key="category.id"
                        @submit.prevent="upload(category.id)"
                      >
                        <div class="flex items-center">
                          <file-input
                            v-model="file.schedule_file[category.id]"
                            :errors="file.errors.schedule_file"
                            accept="file/*"
                            class="w-full pr-6"
                            type="file"
                          />
                          <Button size="sm" type="submit" variant="secondary">
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
            <TableCell
              class="text-xs font-medium tracking-wider text-gray-700 uppercase"
              colspan="3"
            >
              No Payroll
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <pagination :links="payrolls.links" />
    <div
      v-if="showCreateModal"
      aria-modal="true"
      class="fixed inset-0 z-10 overflow-y-auto"
      role="dialog"
    >
      <div
        class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0"
      >
        <div
          class="bg-opacity-75 fixed inset-0 bg-gray-500"
          @click="closeModal"
        ></div>
        <div
          class="inline-block overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:align-middle"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 id="modal-title" class="text-lg leading-6 font-medium">
                  Create Other Schedule
                </h3>
                <div class="mt-5">
                  <select-input
                    v-model="form.payment_type_id"
                    :errors="form.errors.payment_type_id"
                    class="w-full pr-6 pb-6"
                    label="Payment Type"
                    required
                  >
                    <option class="text-gray-100" disabled value="">
                      Select Payment Type
                    </option>
                    <option
                      v-for="payment_type in payment_types"
                      :key="payment_type.id"
                      :value="payment_type.id"
                    >
                      {{ payment_type.name }}
                    </option>
                  </select-input>

                  <text-input
                    v-model="form.payment_title"
                    :errors="form.errors.payment_title"
                    class="w-full pr-6 pb-6 uppercase"
                    label="Payment Title"
                    required
                  />

                  <fieldset class="flex justify-between">
                    <legend class="sr-only">Pay Commission Charges</legend>
                    <div class="relative flex items-start">
                      <div class="flex h-5 items-center">
                        <input
                          id="paycomm_tenece"
                          v-model="form.paycomm_tenece"
                          aria-describedby="paycomm-tenece-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                          name="paycomm_tenece"
                          type="checkbox"
                        />
                      </div>
                      <div class="ml-3 text-sm">
                        <label
                          class="font-medium text-gray-700"
                          for="paycomm_tenece"
                          >Apply Charges</label
                        >
                        <p
                          id="paycomm-tenece-description"
                          class="text-gray-500"
                        >
                          Adds Paycomm II Line Item
                        </p>
                      </div>
                    </div>
                    <div
                      v-if="form.paycomm_tenece"
                      class="relative flex items-start"
                    >
                      <div class="flex h-5 items-center">
                        <input
                          id="paycomm_fidelity"
                          v-model="form.paycomm_fidelity"
                          aria-describedby="paycomm_fidelity-description"
                          class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                          name="paycomm_fidelity"
                          type="checkbox"
                        />
                      </div>
                      <div class="ml-3 text-sm">
                        <label
                          class="font-medium text-gray-700"
                          for="paycomm_fidelity"
                          >Apply Fidelity Charges</label
                        >
                        <p
                          id="paycomm_fidelity-description"
                          class="text-gray-500"
                        >
                          Adds Paycomm I Line Item
                        </p>
                      </div>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
            <Button class="sm:ml-3" type="button" @click="saveSchedule">
              Save
            </Button>
            <Button
              class="sm:ml-3"
              type="button"
              variant="outline"
              @click="closeModal"
            >
              Cancel
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table';
import FileInput from '@/Shared/FileInput';
import Icon from '@/Shared/Icon';
import Layout from '@/Shared/Layout';
import Pagination from '@/Shared/Pagination';
import SelectInput from '@/Shared/SelectInput';
import TextInput from '@/Shared/TextInput';

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
    });
    const file = useForm({
      schedule_file: [],
    });

    return { form, file };
  },

  data() {
    return {
      show_detail: [],
      payroll_id: null,
      showCreateModal: false,
    };
  },

  methods: {
    show(payroll) {
      this.show_detail[payroll] = !this.show_detail[payroll];
    },

    saveSchedule() {
      this.form
        .transform((data) => ({
          ...data,
          audit_payroll_id: this.payroll_id,
        }))
        .post(this.route('other_audit_payroll.store'), {
          preserveScroll: true,
          onSuccess: () => this.closeModal(),
        });
    },

    upload(category_id) {
      this.file
        .transform((data) => ({
          other_audit_payroll_category_id: category_id,
          schedule_file: data.schedule_file[category_id],
        }))
        .post('/other_audit_schedule/store', {
          preserveScroll: true,
        });
    },

    showModal(payroll_id) {
      this.payroll_id = payroll_id;
      this.showCreateModal = true;
    },

    closeModal() {
      this.showCreateModal = false;
      this.form.reset();
    },

    addLeaveAllowance(payroll_id) {
      let result = confirm(
        'Are you sure you want to add Annual Leave Allowance',
      );

      if (result) {
        this.$inertia.get(
          route('audit_payroll.leave', { audit_payroll: payroll_id }),
        );
      }
    },
  },
};
</script>
