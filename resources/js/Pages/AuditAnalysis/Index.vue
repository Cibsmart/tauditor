<template>
  <div>
    <Head title="Audit Analysis" />
    <h1 class="mb-8 font-bold text-3xl">Audit Analysis</h1>
    <div class="mb-6 flex justify-between items-center">
      <div></div>
    </div>

    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Month</TableHead>
            <TableHead>Created By</TableHead>
            <TableHead class="text-right">Details</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody v-for="payroll in payrolls.data" :key="payroll.id">
          <TableRow :key="payroll.id">
            <TableCell>
              <Link class="" href="#" preserve-scroll preserve-state @click="show(payroll.id)">
                <div class="text-sm leading-5 font-medium  uppercase">
                  {{ payroll.month }}
                </div>
                <div class="text-sm leading-5 text-muted-foreground">{{ payroll.year }}</div>
              </Link>
            </TableCell>
            <TableCell>
              <Link class="" href="#" preserve-scroll preserve-state @click="show(payroll.id)">
                <div class="text-sm leading-5 ">
                  {{ payroll.created_by }}
                </div>
                <div class="text-sm leading-5 text-muted-foreground">
                  {{ payroll.date_created }}
                </div>
              </Link>
            </TableCell>
            <TableCell class="w-px text-sm leading-5 font-medium">
              <Link class="px-6" href="#" preserve-scroll preserve-state @click="show(payroll.id)">
                <icon class="block w-6 h-4 fill-gray-400" name="cheveron-right" />
              </Link>
            </TableCell>
          </TableRow>

          <TableRow v-if="show_detail[payroll.id]">
            <TableCell colspan="6">
              <Table>
                <TableBody>
                  <TableRow v-for="category in payroll.categories" :key="category.id">
                    <TableCell>
                      {{ category.payment_title }}
                      <span
                        :class="category.payment_type_id === 'sal' ? 'bg-green-100 text-green-800' : 'bg-pink-100 text-pink-800'"
                        class="px-2 text-xs leading-5 font-semibold rounded-full uppercase">
                                                {{ category.payment_type }}
                                            </span>
                    </TableCell>
                    <TableCell>
                      MDA Count:
                      <span class="font-bold">
                                                {{ category.mda_count }}
                                            </span>
                    </TableCell>
                    <TableCell>
                      Uploaded:
                      <span class="font-bold">
                                                {{ category.uploaded_count }}
                                            </span>
                    </TableCell>
                    <TableCell>
                      Analysed:
                      <span class="font-bold">
                                                {{ category.analysed_count }}
                                            </span>
                    </TableCell>
                    <TableCell>
                                            <span :class="status[category.analysis_status]"
                                                  class="px-2 text-xs leading-5 font-semibold rounded-full uppercase">
                                                {{ category.analysis_status }}
                                            </span>
                    </TableCell>
                    <TableCell class="text-right">
                      <Button v-show="category.analysable" :as="Link"
                              :href="route('audit_analysis.analyse', { audit_payroll_category: category.id })"
                              method="post" preserve-scroll
                              preserve-state size="sm">
                        Analyse
                      </Button>

                      <Link v-show="category.refreshable"
                            :href="route('audit_analysis.index')"
                            class="px-5 py-3" preserve-scroll preserve-state>
                        Refresh
                      </Link>

                      <span v-show="category.analysable && category.viewable"> | </span>

                      <Link v-show="category.viewable"
                            :href="route('audit_analysis.show', { audit_payroll_category: category.id })"
                            class="px-5 py-3" preserve-scroll preserve-state>
                        View Reports
                      </Link>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </TableCell>
          </TableRow>
        </TableBody>

        <TableBody>
          <TableRow v-if="payrolls.data.length === 0">
            <TableCell class="text-xs font-medium text-gray-700 uppercase tracking-wider" colspan="6">
              No Payroll
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <pagination :links="payrolls.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import { Link } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'

export default {
  layout: Layout,

  props: {
    payrolls: Object,
  },

  components: {
    Icon,
    Link,
    Pagination,
    Button,
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
  },

  data() {
    return {
      status: {
        pending: 'bg-yellow-100 text-yellow-800',
        running: 'bg-pink-100 text-pink-800',
        completed: 'bg-green-100 text-green-800',
        incomplete: 'bg-blue-100 text-blue-800',
      },
      show_detail: [],
    }
  },

  methods: {
    show(payroll) {
      this.show_detail[payroll] = !this.show_detail[payroll]
    },
  },
}
</script>
