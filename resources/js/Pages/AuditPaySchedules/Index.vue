<template>
  <div>
    <Head title="Pay Schedules" />
    <h1 class="mb-4 text-3xl font-bold">
      <Link :href="route('audit_payroll.index')"> Audit Payroll</Link>
      <span class="font-medium">/</span>
      <Link
        :href="
          route('audit_mda_schedules.index', {
            audit_payroll_category,
          })
        "
      >
        MDA Schedules
      </Link>
      <span class="font-medium">/</span>
      <Link
        :href="
          route('audit_sub_mda_schedules.index', {
            audit_mda_schedule,
          })
        "
      >
        Sub MDA Schedules
      </Link>
      <span class="font-medium">/</span> Pay Schedules
    </h1>

    <div class="mb-6 flex items-center justify-between">
      <div></div>
    </div>

    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Beneficiary Name/Verification Number </TableHead>
            <TableHead>Cadre/Designation</TableHead>
            <TableHead>Bank Details</TableHead>
            <TableHead>Net Pay</TableHead>
            <TableHead>Payment Status</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="schedule in schedules.data" :key="schedule.id">
            <TableCell>
              <div class="flex items-center">
                <div class="ml-4">
                  <div class="text-sm leading-5 font-medium uppercase">
                    {{ schedule.beneficiary_name }}
                  </div>
                  <div class="text-sm leading-5 text-muted-foreground">
                    {{ schedule.verification_number }}
                  </div>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <div class="text-sm leading-5">
                {{ schedule.cadre }}
              </div>
              <div class="text-sm leading-5 text-muted-foreground">
                {{ schedule.designation }}
              </div>
            </TableCell>
            <TableCell>
              <div class="text-sm leading-5">
                {{ schedule.bank_name }}
              </div>
              <div class="text-sm leading-5 text-muted-foreground">
                {{ schedule.account_number }}
              </div>
            </TableCell>

            <TableCell>
              <div class="text-sm leading-5">
                <span class="line-through">N</span>
                {{ schedule.net_pay }}
              </div>
            </TableCell>

            <TableCell>
              <span
                :class="
                  schedule.paid
                    ? 'bg-green-100 text-green-800'
                    : 'bg-red-100 text-red-800'
                "
                class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold uppercase"
              >
                {{ schedule.paid ? 'Paid' : 'Pending' }}
              </span>
            </TableCell>

            <TableCell class="text-right">
              <Link class="px-5 py-3" href="#"> View Details </Link>
            </TableCell>
          </TableRow>

          <TableRow v-if="schedules.data.length === 0">
            <TableCell
              class="text-xs font-medium tracking-wider uppercase"
              colspan="6"
            >
              No Pay Schedule
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <pagination :links="schedules.links" />
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table';
import Layout from '@/Shared/Layout';
import Pagination from '@/Shared/Pagination';

export default {
  layout: Layout,

  props: {
    schedules: Object,
    audit_payroll_category: Number,
    audit_mda_schedule: Number,
  },

  components: {
    Link,
    Pagination,
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
  },
};
</script>
