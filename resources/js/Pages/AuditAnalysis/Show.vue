<template>
  <div>
    <Head title="Audit Report" />
    <h1 class="mb-8 text-3xl font-bold">Audit Report for {{}} Payment</h1>
    <div class="mb-6 flex items-center justify-between">
      <div></div>
      <Button
        :href="
          route('audit_analysis.pdf_report', {
            audit_payroll_category: audit_payroll_category,
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
            <TableHead>Beneficiary Name</TableHead>
            <TableHead>Report(s) | Current Value | Previous Value </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="report in reports.data" :key="report.id">
            <TableCell>
              <div class="text-sm leading-5 font-medium">
                {{ report.schedule.beneficiary_name }}
              </div>
              <div class="text-sm leading-5 text-muted-foreground">
                {{ report.schedule.verification_number }}
              </div>
            </TableCell>

            <TableCell>
              <Table>
                <TableBody>
                  <TableRow
                    v-for="audit_report in report.reports"
                    :key="audit_report.id"
                  >
                    <TableCell class="w-2/4 py-1">
                      <div
                        class="text-sm leading-5 font-medium text-muted-foreground"
                      >
                        {{ audit_report.message }}
                      </div>
                    </TableCell>
                    <TableCell class="w-1/4 py-1">
                      <div
                        class="text-sm leading-5 font-medium text-muted-foreground"
                      >
                        {{ audit_report.current_value }}
                      </div>
                    </TableCell>
                    <TableCell class="w-1/4 py-1">
                      <div
                        class="text-sm leading-5 font-medium text-muted-foreground"
                      >
                        {{ audit_report.previous_value }}
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </TableCell>
          </TableRow>

          <TableRow v-if="reports.data.length === 0">
            <TableCell
              class="text-xs font-medium tracking-wider uppercase"
              colspan="6"
            >
              No Audit Report
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <pagination :links="reports.links" />
  </div>
</template>

<script>
import { Button } from '@/Components/ui/button';
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
    reports: Object,
    audit_payroll_category: Number,
  },

  components: {
    Pagination,
    Button,
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
  },
};
</script>
