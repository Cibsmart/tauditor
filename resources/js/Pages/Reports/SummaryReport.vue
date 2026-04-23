<template>
  <div>
    <Head title="Payment Summary Report" />
    <h1 class="mb-8 text-3xl font-bold">Payment Summary Report</h1>

    <div class="w-full space-y-3">
      <label class="text-sm leading-none font-medium">Payroll Month</label>
      <Select v-model="form.payroll" @update:modelValue="payrollChanged">
        <SelectTrigger class="w-full">
          <SelectValue placeholder="Select Payroll Month" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem
            v-for="payroll in payrolls"
            :key="payroll.id"
            :value="payroll.id"
          >
            {{ payroll.month_name + ' ' + payroll.year }}
          </SelectItem>
        </SelectContent>
      </Select>
      <p v-if="$page.props.errors.payroll" class="text-sm text-destructive">
        {{ $page.props.errors.payroll }}
      </p>
    </div>

    <div v-show="categories.data">
      <div class="mt-2 mb-6 flex items-center justify-between">
        <div></div>
        <Button
          :href="
            route('reports.summary_print', {
              payroll: form.payroll,
            })
          "
          as="a"
          size="lg"
        >
          Download<span class="hidden md:inline">&nbsp; PDF</span>
        </Button>
      </div>

      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Payment Category Title</TableHead>
              <TableHead>Total Net Pay</TableHead>
              <TableHead>Head Count</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="category in categories.data" :key="category.id">
              <TableCell>
                <div class="text-sm leading-5 font-medium uppercase">
                  {{ category.payment_title }}
                </div>
              </TableCell>
              <TableCell>
                <div class="text-sm leading-5 font-medium uppercase">
                  <span class="line-through">N</span>
                  {{ category.total_net_pay }}
                </div>
              </TableCell>
              <TableCell>
                <div class="text-sm leading-5 font-medium uppercase">
                  {{ category.head_count }}
                </div>
              </TableCell>
            </TableRow>

            <TableRow>
              <TableCell>
                <div class="text-sm leading-5 font-bold uppercase">
                  {{ 'Total' }}
                </div>
              </TableCell>
              <TableCell>
                <div class="text-sm leading-5 font-bold uppercase">
                  <span class="line-through">N</span>
                  {{ payroll.total_net_pay }}
                </div>
              </TableCell>
              <TableCell>
                <div class="text-sm leading-5 font-bold uppercase">
                  {{ payroll.head_count }}
                </div>
              </TableCell>
            </TableRow>

            <TableRow v-if="categories.data && categories.data.length === 0">
              <TableCell
                class="text-xs font-medium tracking-wider uppercase"
                colspan="6"
              >
                No Payment Summary
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </div>
</template>

<script>
import { router, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
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
import Layout from '@/Shared/Layout';

export default {
  layout: Layout,

  props: {
    payroll: Object,
    payrolls: Array,
    categories: Object,
  },

  components: {
    Select,
    SelectItem,
    SelectTrigger,
    SelectValue,
    SelectContent,
    Button,
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
  },

  setup(props) {
    const form = useForm({
      payroll: props.payroll.id,
    });

    return { form };
  },

  methods: {
    payrollChanged() {
      router.reload({
        method: 'post',
        data: this.form,
        preserveState: true,
        preserveScroll: true,
        only: ['categories', 'payroll'],
      });
    },
  },
};
</script>
