<template>
  <div>
    <Head title="Audit Payroll" />

    <div class="mb-6 flex items-start justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">Audit Payrolls</h1>
        <p class="text-sm text-muted-foreground">
          Manage monthly payroll schedules and categories
        </p>
      </div>
      <Button asChild size="lg">
        <Link :href="route('audit_payroll.store')" method="post" preserve-state>
          Add <span class="hidden md:inline">&nbsp;Payroll</span>
        </Link>
      </Button>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
      <Card>
        <CardContent class="flex items-center gap-4 p-6">
          <div
            class="flex h-12 w-12 items-center justify-center rounded-md bg-primary/10 text-primary"
          >
            <LayoutList class="h-6 w-6" />
          </div>
          <div>
            <div class="text-2xl font-bold">{{ totalPayrolls }}</div>
            <p class="text-xs text-muted-foreground">Total Payrolls</p>
          </div>
        </CardContent>
      </Card>
      <Card>
        <CardContent class="flex items-center gap-4 p-6">
          <div
            class="flex h-12 w-12 items-center justify-center rounded-md bg-primary/10 text-primary"
          >
            <FolderOpen class="h-6 w-6" />
          </div>
          <div>
            <div class="text-2xl font-bold">{{ totalCategories }}</div>
            <p class="text-xs text-muted-foreground">Total Categories</p>
          </div>
        </CardContent>
      </Card>
      <Card>
        <CardContent class="flex items-center gap-4 p-6">
          <div
            class="flex h-12 w-12 items-center justify-center rounded-md bg-primary/10 text-primary"
          >
            <CalendarDays class="h-6 w-6" />
          </div>
          <div>
            <div class="text-2xl font-bold">{{ mostRecentPeriod }}</div>
            <p class="text-xs text-muted-foreground">Most Recent Period</p>
          </div>
        </CardContent>
      </Card>
    </div>

    <Card class="mb-6">
      <CardContent class="flex flex-col gap-3 p-4 md:flex-row md:items-center">
        <div class="relative flex-1">
          <Search
            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
          />
          <Input
            v-model="searchQuery"
            class="pl-9"
            placeholder="Search by category name..."
            type="text"
          />
        </div>
        <Select v-model="filterMonth">
          <SelectTrigger class="md:w-44">
            <SelectValue placeholder="All months" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem
              v-for="month in monthOptions"
              :key="month.value"
              :value="month.value"
            >
              {{ month.label }}
            </SelectItem>
          </SelectContent>
        </Select>
        <Select v-model="filterYear">
          <SelectTrigger class="md:w-32">
            <SelectValue placeholder="All years" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem
              v-for="year in yearOptions"
              :key="year"
              :value="String(year)"
            >
              {{ year }}
            </SelectItem>
          </SelectContent>
        </Select>
        <Button
          v-if="hasActiveFilters"
          size="sm"
          variant="ghost"
          @click="clearFilters"
        >
          Clear
        </Button>
      </CardContent>
    </Card>

    <div v-if="payrolls.data.length === 0">
      <Card>
        <CardContent
          class="flex flex-col items-center justify-center gap-3 py-16 text-center"
        >
          <CalendarDays class="h-10 w-10 text-muted-foreground" />
          <p class="text-sm text-muted-foreground">
            No payroll schedules yet. Use the "Add Payroll" button to create
            one.
          </p>
        </CardContent>
      </Card>
    </div>

    <div v-else-if="filteredPayrolls.length === 0">
      <Card>
        <CardContent
          class="flex flex-col items-center justify-center gap-3 py-16 text-center"
        >
          <Search class="h-10 w-10 text-muted-foreground" />
          <p class="text-sm text-muted-foreground">
            No payrolls match your filters.
          </p>
          <Button size="sm" variant="ghost" @click="clearFilters">
            Clear Filters
          </Button>
        </CardContent>
      </Card>
    </div>

    <div v-else class="space-y-4">
      <Card v-for="payroll in filteredPayrolls" :key="payroll.id">
        <CardHeader>
          <div
            class="flex cursor-pointer flex-col gap-3 md:flex-row md:items-center md:justify-between"
            @click="show(payroll.id)"
          >
            <div class="flex items-center gap-3">
              <ChevronUp v-if="show_detail[payroll.id]" class="h-5 w-5" />
              <ChevronDown v-else class="h-5 w-5" />
              <div>
                <div class="text-base font-semibold uppercase">
                  {{ payroll.month }} {{ payroll.year }}
                </div>
                <div class="text-xs text-muted-foreground">
                  {{ payroll.created_by }} &middot; {{ payroll.date_created }}
                </div>
              </div>
              <Badge variant="secondary">
                {{
                  payroll.categories.length + payroll.other_categories.length
                }}
                categories
              </Badge>
            </div>
            <div class="flex flex-wrap items-center gap-2" @click.stop>
              <Button
                v-if="payroll.is_current && payroll.can_add_leave"
                size="sm"
                variant="outline"
                @click.prevent="addLeaveAllowance(payroll.id)"
              >
                Add Annual Leave Allowance
              </Button>
              <Button
                size="sm"
                variant="outline"
                @click.prevent="showModal(payroll.id)"
              >
                Add Schedule
              </Button>
            </div>
          </div>
        </CardHeader>

        <Transition name="slide">
          <CardContent v-if="show_detail[payroll.id]">
            <div v-if="payroll.categories.length > 0">
              <p
                v-if="payroll.other_categories.length > 0"
                class="mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
              >
                Standard Categories
              </p>
              <div class="overflow-x-auto rounded-md border">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Category Name</TableHead>
                      <TableHead>Payment Type</TableHead>
                      <TableHead>Total Amount</TableHead>
                      <TableHead>Headcount</TableHead>
                      <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow
                      v-for="category in payroll.categories"
                      :key="category.id"
                    >
                      <TableCell class="font-medium">
                        {{ category.payment_title }}
                      </TableCell>
                      <TableCell>
                        <Badge
                          :class="
                            category.payment_type_id === 'sal'
                              ? 'bg-green-100 text-green-800 hover:bg-green-100'
                              : 'bg-pink-100 text-pink-800 hover:bg-pink-100'
                          "
                          :variant="
                            category.payment_type_id === 'sal'
                              ? 'default'
                              : 'destructive'
                          "
                        >
                          {{ category.payment_type }}
                        </Badge>
                      </TableCell>
                      <TableCell class="font-bold">
                        ₦{{ category.total_amount }}
                      </TableCell>
                      <TableCell>{{ category.head_count }}</TableCell>
                      <TableCell class="text-right">
                        <Button asChild size="sm" variant="outline">
                          <Link
                            :href="
                              route('audit_mda_schedules.index', {
                                audit_payroll_category: category.id,
                              })
                            "
                            preserve-scroll
                            preserve-state
                          >
                            View MDAs
                          </Link>
                        </Button>
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
              </div>
            </div>

            <div v-if="payroll.other_categories.length > 0">
              <p
                v-if="payroll.categories.length > 0"
                class="mt-4 mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
              >
                Other Categories
              </p>
              <div class="overflow-x-auto rounded-md border">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Category Name</TableHead>
                      <TableHead>Payment Type</TableHead>
                      <TableHead>Total Amount</TableHead>
                      <TableHead>Headcount</TableHead>
                      <TableHead class="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow
                      v-for="category in payroll.other_categories"
                      :key="category.id"
                    >
                      <TableCell>
                        <div class="font-medium">
                          {{ category.payment_title }}
                        </div>
                        <span
                          v-if="!category.tenece && !category.fidelity"
                          class="text-xs text-green-900 italic"
                        >
                          No Charge Applied
                        </span>
                        <span
                          v-else-if="category.tenece && category.fidelity"
                          class="text-xs text-pink-900 italic"
                        >
                          All Charges Applied
                        </span>
                        <span
                          v-else-if="category.tenece && !category.fidelity"
                          class="text-xs text-blue-900 italic"
                        >
                          Fidelity Charge not Applied
                        </span>
                      </TableCell>
                      <TableCell>
                        <Badge :class="category.color">
                          {{ category.payment_type }}
                        </Badge>
                      </TableCell>
                      <TableCell class="font-bold">
                        ₦{{ category.total_amount }}
                      </TableCell>
                      <TableCell>{{ category.head_count }}</TableCell>
                      <TableCell class="text-right">
                        <Button
                          v-if="category.uploaded"
                          asChild
                          size="sm"
                          variant="outline"
                        >
                          <Link href="#" preserve-scroll preserve-state>
                            View Schedule
                          </Link>
                        </Button>
                        <form
                          v-else
                          :key="category.id"
                          @submit.prevent="upload(category.id)"
                        >
                          <div class="flex items-center justify-end gap-2">
                            <file-input
                              v-model="file.schedule_file[category.id]"
                              :errors="file.errors.schedule_file"
                              accept="file/*"
                              class="w-full"
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
              </div>
            </div>

            <p class="mt-2 pr-2 text-right text-sm text-muted-foreground">
              ₦{{ payrollTotalAmount(payroll) }} &middot;
              {{ payrollHeadcount(payroll) }} headcount &middot;
              {{ payroll.categories.length + payroll.other_categories.length }}
              categories
            </p>
          </CardContent>
        </Transition>
      </Card>
    </div>

    <pagination :links="payrolls.links" />

    <Dialog
      :open="showCreateModal"
      @update:open="(open) => !open && closeModal()"
    >
      <DialogContent class="max-w-2xl">
        <DialogHeader>
          <DialogTitle>Create Other Schedule</DialogTitle>
        </DialogHeader>

        <div class="space-y-4">
          <select-input
            v-model="form.payment_type_id"
            :errors="form.errors.payment_type_id"
            class="w-full"
            label="Payment Type"
            required
          >
            <option disabled value="">Select Payment Type</option>
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
            class="w-full uppercase"
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
                  class="h-4 w-4 rounded"
                  name="paycomm_tenece"
                  type="checkbox"
                />
              </div>
              <div class="ml-3 text-sm">
                <label class="font-medium" for="paycomm_tenece"
                  >Apply Charges</label
                >
                <p
                  id="paycomm-tenece-description"
                  class="text-muted-foreground"
                >
                  Adds Paycomm II Line Item
                </p>
              </div>
            </div>
            <div v-if="form.paycomm_tenece" class="relative flex items-start">
              <div class="flex h-5 items-center">
                <input
                  id="paycomm_fidelity"
                  v-model="form.paycomm_fidelity"
                  aria-describedby="paycomm_fidelity-description"
                  class="h-4 w-4 rounded"
                  name="paycomm_fidelity"
                  type="checkbox"
                />
              </div>
              <div class="ml-3 text-sm">
                <label class="font-medium" for="paycomm_fidelity"
                  >Apply Fidelity Charges</label
                >
                <p
                  id="paycomm_fidelity-description"
                  class="text-muted-foreground"
                >
                  Adds Paycomm I Line Item
                </p>
              </div>
            </div>
          </fieldset>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="closeModal">
            Cancel
          </Button>
          <Button type="button" @click="saveSchedule"> Save</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3';
import {
  CalendarDays,
  ChevronDown,
  ChevronUp,
  FolderOpen,
  LayoutList,
  Search,
} from 'lucide-vue-next';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader } from '@/Components/ui/card';
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table';
import FileInput from '@/Shared/FileInput';
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
    Link,
    TextInput,
    FileInput,
    Pagination,
    SelectInput,
    Badge,
    Button,
    Card,
    CardContent,
    CardHeader,
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    Input,
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
    CalendarDays,
    ChevronDown,
    ChevronUp,
    FolderOpen,
    LayoutList,
    Search,
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
      show_detail: {},
      payroll_id: null,
      showCreateModal: false,
      searchQuery: '',
      filterMonth: '',
      filterYear: '',
    };
  },

  computed: {
    totalPayrolls() {
      return this.payrolls.data.length;
    },
    totalCategories() {
      return this.payrolls.data.reduce(
        (sum, p) => sum + p.categories.length + p.other_categories.length,
        0,
      );
    },
    mostRecentPeriod() {
      if (!this.payrolls.data.length) {
        return '—';
      }

      const first = this.payrolls.data[0];

      return `${first.month} ${first.year}`;
    },
    monthOptions() {
      return [
        'JANUARY',
        'FEBRUARY',
        'MARCH',
        'APRIL',
        'MAY',
        'JUNE',
        'JULY',
        'AUGUST',
        'SEPTEMBER',
        'OCTOBER',
        'NOVEMBER',
        'DECEMBER',
      ].map((m) => ({
        value: m,
        label: m.charAt(0) + m.slice(1).toLowerCase(),
      }));
    },
    yearOptions() {
      const current = new Date().getFullYear();

      return [0, 1, 2, 3, 4].map((i) => current - i);
    },
    hasActiveFilters() {
      return !!(this.searchQuery || this.filterMonth || this.filterYear);
    },
    filteredPayrolls() {
      return this.payrolls.data
        .map((payroll) => {
          const allCats = [
            ...payroll.categories.map((c) => ({ ...c, _type: 'standard' })),
            ...payroll.other_categories.map((c) => ({ ...c, _type: 'other' })),
          ];
          const filtered = !this.searchQuery
            ? allCats
            : allCats.filter((c) =>
                c.payment_title
                  .toLowerCase()
                  .includes(this.searchQuery.toLowerCase()),
              );

          return { ...payroll, _filteredCategories: filtered };
        })
        .filter((payroll) => {
          const monthMatch =
            !this.filterMonth || payroll.month === this.filterMonth;
          const yearMatch =
            !this.filterYear || String(payroll.year) === this.filterYear;
          const hasCategories =
            !this.searchQuery || payroll._filteredCategories.length > 0;

          return monthMatch && yearMatch && hasCategories;
        });
    },
  },

  methods: {
    show(payroll) {
      this.show_detail[payroll] = !this.show_detail[payroll];
    },

    payrollHeadcount(payroll) {
      const sum = (cats) =>
        cats.reduce((s, c) => s + (Number(c.headCount) || 0), 0);
      const total = sum(payroll.categories) + sum(payroll.other_categories);

      return total.toLocaleString();
    },

    payrollTotalAmount(payroll) {
      const sum = (cats) =>
        cats.reduce((s, c) => s + (Number(c.totalAmount) || 0), 0);
      const total = sum(payroll.categories) + sum(payroll.other_categories);

      return total.toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },

    clearFilters() {
      this.searchQuery = '';
      this.filterMonth = '';
      this.filterYear = '';
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

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.2s ease;
  overflow: hidden;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  max-height: 0;
}

.slide-enter-to,
.slide-leave-from {
  opacity: 1;
  max-height: 2000px;
}
</style>
