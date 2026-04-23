<template>
  <div>
    <Head title="Audit Autopay" />
    <h1 class="mb-8 text-3xl font-bold">Auto Pay Payrolls</h1>
    <div class="mb-6 flex items-center justify-between">
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
              <Link
                class=""
                href="#"
                preserve-scroll
                preserve-state
                @click="show(payroll.id)"
              >
                <div class="text-sm leading-5 font-medium uppercase">
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
                    <TableCell class="">
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
                    <TableCell class="">
                      MDA Count:
                      <span class="font-bold">
                        {{ category.mda_count }}
                      </span>
                    </TableCell>
                    <TableCell class="">
                      Uploaded:
                      <span class="font-bold">
                        {{ category.uploaded_count }}
                      </span>
                    </TableCell>
                    <TableCell class="">
                      Generated:
                      <span class="font-bold">
                        {{ category.autopay_count }}
                      </span>
                    </TableCell>
                    <TableCell class="">
                      <span
                        :class="status[category.autopay_status]"
                        class="rounded-full px-2 text-xs leading-5 font-semibold uppercase"
                      >
                        {{ category.autopay_status }}
                      </span>
                    </TableCell>
                    <TableCell class="text-right">
                      <Button
                        v-show="category.can_generate"
                        :as="Link"
                        :href="
                          route('audit_autopay.generate', {
                            audit_payroll_category: category.id,
                          })
                        "
                        method="post"
                        preserve-scroll
                        preserve-state
                        size="sm"
                      >
                        Generate
                      </Button>

                      <Link
                        v-show="category.refreshable"
                        :href="route('audit_autopay.index')"
                        class="px-5 py-3"
                        preserve-scroll
                        preserve-state
                      >
                        Refresh
                      </Link>

                      <a
                        v-show="category.viewable"
                        :href="
                          route('audit_autopay.download', {
                            audit_payroll_category: category.id,
                          })
                        "
                        class="px-5 py-3"
                      >
                        Download Autopay
                      </a>

                      <span v-show="category.viewable"> | </span>

                      <a
                        v-show="category.viewable"
                        :href="
                          route('audit_autopay.downloadMfb', {
                            audit_payroll_category: category.id,
                          })
                        "
                        class="px-5 py-3"
                      >
                        Download MFB
                      </a>

                      <span v-show="category.viewable"> | </span>

                      <Link
                        v-show="category.viewable"
                        :href="
                          route('audit_autopay.show', {
                            audit_payroll_category: category.id,
                          })
                        "
                        class="px-5 py-3"
                        preserve-scroll
                        preserve-state
                      >
                        View MDAs
                      </Link>
                    </TableCell>
                  </TableRow>

                  <TableRow
                    v-for="category in payroll.other_categories"
                    :key="category.id"
                  >
                    <TableCell class="">
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
                    <TableCell class="">
                      Line Items:
                      <span class="font-bold">
                        {{ category.line_items }}
                      </span>
                    </TableCell>
                    <TableCell class="">
                      <span
                        :class="
                          category.uploaded
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                        "
                        class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold"
                      >
                        {{ category.uploaded ? 'UPLOADED' : 'NOT-UPLOADED' }}
                      </span>
                    </TableCell>
                    <TableCell class="">
                      <span
                        :class="
                          category.autopay_generated
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                        "
                        class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold"
                      >
                        {{
                          category.autopay_generated
                            ? 'GENERATED'
                            : 'NOT-GENERATED'
                        }}
                      </span>
                    </TableCell>
                    <TableCell class="">
                      <span
                        :class="status[category.autopay_status]"
                        class="rounded-full px-2 text-xs leading-5 font-semibold uppercase"
                      >
                        {{ category.autopay_status }}
                      </span>
                    </TableCell>
                    <TableCell class="text-right">
                      <Button
                        v-show="category.can_generate"
                        :as="Link"
                        :href="
                          route('other_audit_autopay.generate', {
                            other_audit_payroll_category: category.id,
                          })
                        "
                        method="post"
                        preserve-scroll
                        preserve-state
                        size="sm"
                      >
                        Generate
                      </Button>

                      <Link
                        v-show="category.refreshable"
                        :href="route('audit_autopay.index')"
                        class="px-5 py-3"
                        preserve-scroll
                        preserve-state
                      >
                        Refresh
                      </Link>

                      <a
                        v-show="category.viewable"
                        :href="
                          route('other_audit_autopay.download', {
                            other_audit_payroll_category: category.id,
                          })
                        "
                        class="px-5 py-3"
                      >
                        Download Autopay
                      </a>

                      <span v-show="category.viewable"> | </span>

                      <a
                        v-show="category.viewable"
                        :href="
                          route('other_audit_autopay.downloadMfb', {
                            other_audit_payroll_category: category.id,
                          })
                        "
                        class="px-5 py-3"
                      >
                        Download MFB
                      </a>

                      <span v-show="category.viewable"> | </span>

                      <Link
                        v-show="category.viewable"
                        class="px-5 py-3"
                        href="#"
                        preserve-scroll
                        preserve-state
                      >
                        View Schedule
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
            <TableCell
              class="text-xs font-medium tracking-wider text-gray-700 uppercase"
              colspan="6"
            >
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
import { Link } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table';
import Icon from '@/Shared/Icon';
import Layout from '@/Shared/Layout';
import Pagination from '@/Shared/Pagination';

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
    };
  },

  methods: {
    show(payroll) {
      this.show_detail[payroll] = !this.show_detail[payroll];
    },
  },
};
</script>
