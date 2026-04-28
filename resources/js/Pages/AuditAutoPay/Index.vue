<template>
  <div>
    <Head title="Audit Autopay" />
    <h1 class="mb-6 text-3xl font-bold">Auto Pay Payrolls</h1>

    <!-- Filter bar -->
    <div class="mb-6 flex flex-wrap items-center gap-3">
      <Input
        v-model="searchQuery"
        class="w-64"
        placeholder="Search payrolls…"
      />
      <Select v-model="filterMonth">
        <SelectTrigger class="w-40">
          <SelectValue placeholder="All Months" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="">All Months</SelectItem>
          <SelectItem v-for="month in uniqueMonths" :key="month" :value="month">
            {{ month.toUpperCase() }}
          </SelectItem>
        </SelectContent>
      </Select>
      <Select v-model="filterYear">
        <SelectTrigger class="w-32">
          <SelectValue placeholder="All Years" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="">All Years</SelectItem>
          <SelectItem v-for="year in uniqueYears" :key="year" :value="year">
            {{ year }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <!-- Empty: no payrolls in the dataset at all -->
    <div v-if="payrolls.data.length === 0" class="flex justify-center py-16">
      <Card class="w-full max-w-sm">
        <CardContent class="flex flex-col items-center gap-3 py-10 text-center">
          <CalendarDays class="h-10 w-10 text-muted-foreground" />
          <p class="text-sm text-muted-foreground">
            No autopay payrolls available.
          </p>
        </CardContent>
      </Card>
    </div>

    <!-- Empty: filters produced no results -->
    <div
      v-else-if="filteredPayrolls.length === 0"
      class="flex justify-center py-16"
    >
      <Card class="w-full max-w-sm">
        <CardContent class="flex flex-col items-center gap-3 py-10 text-center">
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

    <!-- Payroll cards -->
    <div v-else class="space-y-4">
      <Card
        v-for="payroll in filteredPayrolls"
        :key="payroll.id"
        class="overflow-hidden"
      >
        <!-- Always-visible header — click to toggle -->
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
            </div>
            <div class="flex flex-wrap items-center gap-2" @click.stop>
              <Badge variant="secondary">
                {{
                  payroll.categories.length + payroll.other_categories.length
                }}
                categories
              </Badge>
            </div>
          </div>
        </CardHeader>

        <!-- Collapsible detail -->
        <Transition name="slide">
          <CardContent v-if="show_detail[payroll.id]" class="pt-0">
            <!-- Section A: Standard Categories -->
            <div class="overflow-x-auto">
              <p
                class="mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
              >
                Standard Categories
              </p>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Category</TableHead>
                    <TableHead>MDA Count</TableHead>
                    <TableHead>Uploaded</TableHead>
                    <TableHead>Generated</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow
                    v-for="category in payroll.categories"
                    :key="category.id"
                  >
                    <TableCell>
                      <div class="font-medium">
                        {{ category.payment_title }}
                      </div>
                      <Badge
                        :class="
                          category.payment_type_id === 'sal'
                            ? 'bg-green-100 text-green-800'
                            : 'bg-pink-100 text-pink-800'
                        "
                        variant="secondary"
                      >
                        {{ category.payment_type }}
                      </Badge>
                    </TableCell>
                    <TableCell>{{ category.mda_count }}</TableCell>
                    <TableCell>{{ category.uploaded_count }}</TableCell>
                    <TableCell>{{ category.autopay_count }}</TableCell>
                    <TableCell>
                      <span
                        :class="status[category.autopay_status]"
                        class="rounded-full px-2 text-xs leading-5 font-semibold uppercase"
                      >
                        {{ category.autopay_status }}
                      </span>
                    </TableCell>
                    <TableCell>
                      <div class="flex flex-wrap justify-end gap-1">
                        <Button
                          v-show="category.can_generate"
                          asChild
                          size="sm"
                        >
                          <Link
                            :href="
                              route('audit_autopay.generate', {
                                audit_payroll_category: category.id,
                              })
                            "
                            as="button"
                            method="post"
                            preserve-scroll
                            preserve-state
                          >
                            Generate
                          </Link>
                        </Button>

                        <Button
                          v-show="category.refreshable"
                          asChild
                          size="sm"
                          variant="outline"
                        >
                          <Link
                            :href="route('audit_autopay.index')"
                            preserve-scroll
                            preserve-state
                          >
                            Refresh
                          </Link>
                        </Button>

                        <Button
                          v-show="category.viewable"
                          asChild
                          size="sm"
                          variant="outline"
                        >
                          <a
                            :href="
                              route('audit_autopay.download', {
                                audit_payroll_category: category.id,
                              })
                            "
                          >
                            Download Autopay
                          </a>
                        </Button>

                        <span v-show="category.viewable"> | </span>

                        <template
                          v-if="category.viewable && category.has_mfb_schedule"
                        >
                          <Button
                            v-if="category.mfb_zip_status === 'none'"
                            asChild
                            size="sm"
                            variant="outline"
                          >
                            <Link
                              :href="
                                route('audit_autopay.buildMfb', {
                                  audit_payroll_category: category.id,
                                })
                              "
                              as="button"
                              method="post"
                              preserve-scroll
                              preserve-state
                            >
                              Build MFB
                            </Link>
                          </Button>

                          <Button
                            v-else-if="category.mfb_zip_status === 'building'"
                            disabled
                            size="sm"
                            variant="outline"
                          >
                            <svg
                              class="mr-2 h-3 w-3 animate-spin"
                              fill="none"
                              viewBox="0 0 24 24"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                              />
                              <path
                                class="opacity-75"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                fill="currentColor"
                              />
                            </svg>
                            Preparing MFB…
                          </Button>

                          <Button
                            v-else-if="category.mfb_zip_status === 'ready'"
                            asChild
                            size="sm"
                            variant="outline"
                          >
                            <a
                              :href="
                                route('audit_autopay.downloadMfb', {
                                  audit_payroll_category: category.id,
                                })
                              "
                            >
                              Download MFB
                            </a>
                          </Button>

                          <Button
                            v-else-if="category.mfb_zip_status === 'failed'"
                            asChild
                            class="border-red-500 text-red-700"
                            size="sm"
                            variant="outline"
                          >
                            <Link
                              :href="
                                route('audit_autopay.buildMfb', {
                                  audit_payroll_category: category.id,
                                })
                              "
                              as="button"
                              method="post"
                              preserve-scroll
                              preserve-state
                            >
                              Retry MFB
                            </Link>
                          </Button>
                        </template>

                        <span
                          v-show="
                            category.viewable && !category.has_mfb_schedule
                          "
                          class="text-xs text-muted-foreground"
                        >
                          No MFB
                        </span>

                        <span v-show="category.viewable"> | </span>

                        <Button
                          v-show="category.viewable"
                          asChild
                          size="sm"
                          variant="outline"
                        >
                          <Link
                            v-show="category.viewable"
                            :href="
                              route('audit_autopay.show', {
                                audit_payroll_category: category.id,
                              })
                            "
                            preserve-scroll
                            preserve-state
                          >
                            View MDAs
                          </Link>
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>

            <!-- Section B: Other Categories -->
            <div
              v-if="payroll.other_categories.length > 0"
              class="overflow-x-auto"
            >
              <p
                class="mt-4 mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
              >
                Other Categories
              </p>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Category</TableHead>
                    <TableHead>Line Items</TableHead>
                    <TableHead>Uploaded</TableHead>
                    <TableHead>Generated</TableHead>
                    <TableHead>Status</TableHead>
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
                      <Badge :class="category.color">
                        {{ category.payment_type }}
                      </Badge>
                      <div class="mt-1">
                        <span
                          v-if="!category.tenece && !category.fidelity"
                          class="text-xs text-green-900 italic"
                          >No Charge Applied</span
                        >
                        <span
                          v-if="category.tenece && category.fidelity"
                          class="text-xs text-pink-900 italic"
                          >All Charges Applied</span
                        >
                        <span
                          v-if="category.tenece && !category.fidelity"
                          class="text-xs text-blue-900 italic"
                          >Fidelity Charge not Applied</span
                        >
                      </div>
                    </TableCell>
                    <TableCell>{{ category.line_items }}</TableCell>
                    <TableCell>
                      <Badge
                        :class="
                          category.uploaded
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                        "
                      >
                        {{ category.uploaded ? 'UPLOADED' : 'NOT-UPLOADED' }}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <Badge
                        :class="
                          category.autopay_generated
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                        "
                      >
                        {{
                          category.autopay_generated
                            ? 'GENERATED'
                            : 'NOT-GENERATED'
                        }}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <span
                        :class="status[category.autopay_status]"
                        class="rounded-full px-2 text-xs leading-5 font-semibold uppercase"
                      >
                        {{ category.autopay_status }}
                      </span>
                    </TableCell>
                    <TableCell>
                      <div class="flex flex-wrap justify-end gap-1">
                        <Button
                          v-show="category.can_generate"
                          asChild
                          size="sm"
                        >
                          <Link
                            :href="
                              route('other_audit_autopay.generate', {
                                other_audit_payroll_category: category.id,
                              })
                            "
                            as="button"
                            method="post"
                            preserve-scroll
                            preserve-state
                          >
                            Generate
                          </Link>
                        </Button>

                        <Button
                          v-show="category.refreshable"
                          asChild
                          size="sm"
                          variant="ghost"
                        >
                          <Link
                            :href="route('audit_autopay.index')"
                            preserve-scroll
                            preserve-state
                          >
                            Refresh
                          </Link>
                        </Button>

                        <Button
                          v-show="category.viewable"
                          asChild
                          size="sm"
                          variant="outline"
                        >
                          <a
                            :href="
                              route('other_audit_autopay.download', {
                                other_audit_payroll_category: category.id,
                              })
                            "
                          >
                            Download Autopay
                          </a>
                        </Button>

                        <span v-show="category.viewable"> | </span>

                        <Button
                          v-show="category.viewable"
                          asChild
                          size="sm"
                          variant="outline"
                        >
                          <a
                            :href="
                              route('other_audit_autopay.downloadMfb', {
                                other_audit_payroll_category: category.id,
                              })
                            "
                          >
                            Download MFB
                          </a>
                        </Button>

                        <span v-show="category.viewable"> | </span>

                        <Button
                          v-show="category.viewable"
                          asChild
                          size="sm"
                          variant="ghost"
                        >
                          <Link
                            v-show="category.viewable"
                            class="px-5 py-3"
                            href="#"
                            preserve-scroll
                            preserve-state
                          >
                            View Schedule
                          </Link>
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
          </CardContent>
        </Transition>
      </Card>
    </div>

    <pagination :links="payrolls.links" />
  </div>
</template>

<script>
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarDays, ChevronDown, ChevronUp, Search } from 'lucide-vue-next';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader } from '@/Components/ui/card';
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
import Layout from '@/Shared/Layout';
import Pagination from '@/Shared/Pagination';

export default {
  layout: Layout,

  props: {
    payrolls: Object,
  },

  components: {
    Head,
    Link,
    Pagination,
    Badge,
    Button,
    Card,
    CardContent,
    CardHeader,
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
    Search,
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
      searchQuery: '',
      filterMonth: '',
      filterYear: '',
      pollDelay: 4000,
      pollTimeoutId: null,
      pollInFlight: false,
      prevMfbStatus: {},
      recentlyReady: {},
      firstSeenBuildingAt: {},
    };
  },

  computed: {
    anyMfbBuilding() {
      return this.payrolls.data.some((p) =>
        (p.categories || []).some((c) => c.mfb_zip_status === 'building'),
      );
    },

    uniqueMonths() {
      const months = this.payrolls.data.map((p) => p.month).filter(Boolean);

      return [...new Set(months)];
    },

    uniqueYears() {
      const years = this.payrolls.data
        .map((p) => String(p.year))
        .filter(Boolean);

      return [...new Set(years)];
    },

    filteredPayrolls() {
      const q = this.searchQuery.toLowerCase().trim();

      return this.payrolls.data.filter((p) => {
        const matchesSearch =
          !q ||
          (p.month || '').toLowerCase().includes(q) ||
          String(p.year || '').includes(q) ||
          (p.created_by || '').toLowerCase().includes(q);
        const matchesMonth =
          !this.filterMonth ||
          (p.month || '').toLowerCase() === this.filterMonth.toLowerCase();
        const matchesYear =
          !this.filterYear || String(p.year || '') === String(this.filterYear);

        return matchesSearch && matchesMonth && matchesYear;
      });
    },
  },

  watch: {
    payrolls: {
      handler() {
        this.detectMfbTransitions();
      },
      deep: true,
    },
  },

  mounted() {
    this.snapshotMfbStatus();
    document.addEventListener('visibilitychange', this.onVisibilityChange);

    if (this.anyMfbBuilding) {
      this.schedulePoll();
    }
  },

  beforeUnmount() {
    if (this.pollTimeoutId) {
      clearTimeout(this.pollTimeoutId);
      this.pollTimeoutId = null;
    }

    document.removeEventListener('visibilitychange', this.onVisibilityChange);
  },

  methods: {
    show(payroll) {
      this.show_detail[payroll] = !this.show_detail[payroll];
    },

    clearFilters() {
      this.searchQuery = '';
      this.filterMonth = '';
      this.filterYear = '';
    },

    relativeTime(iso) {
      if (!iso) {
        return '';
      }

      const diffSec = Math.max(
        0,
        Math.floor((Date.now() - new Date(iso)) / 1000),
      );

      if (diffSec < 60) {
        return 'just now';
      }

      if (diffSec < 3600) {
        return `${Math.floor(diffSec / 60)}m ago`;
      }

      if (diffSec < 86400) {
        return `${Math.floor(diffSec / 3600)}h ago`;
      }

      return `${Math.floor(diffSec / 86400)}d ago`;
    },

    snapshotMfbStatus() {
      const snap = {};
      this.payrolls.data.forEach((p) => {
        (p.categories || []).forEach((c) => {
          snap[c.id] = c.mfb_zip_status;

          if (
            c.mfb_zip_status === 'building' &&
            !this.firstSeenBuildingAt[c.id]
          ) {
            this.firstSeenBuildingAt[c.id] = Date.now();
          }
        });
      });
      this.prevMfbStatus = snap;
    },

    detectMfbTransitions() {
      let transitioned = false;
      const nextSnap = {};
      this.payrolls.data.forEach((p) => {
        (p.categories || []).forEach((c) => {
          const prev = this.prevMfbStatus[c.id];
          nextSnap[c.id] = c.mfb_zip_status;

          if (prev !== undefined && prev !== c.mfb_zip_status) {
            transitioned = true;

            if (prev === 'building' && c.mfb_zip_status === 'ready') {
              this.recentlyReady = { ...this.recentlyReady, [c.id]: true };
              setTimeout(() => {
                const next = { ...this.recentlyReady };
                delete next[c.id];
                this.recentlyReady = next;
              }, 3000);
            }
          }

          if (
            c.mfb_zip_status === 'building' &&
            !this.firstSeenBuildingAt[c.id]
          ) {
            this.firstSeenBuildingAt[c.id] = Date.now();
          }

          if (
            c.mfb_zip_status !== 'building' &&
            this.firstSeenBuildingAt[c.id]
          ) {
            delete this.firstSeenBuildingAt[c.id];
          }
        });
      });
      this.prevMfbStatus = nextSnap;

      if (transitioned) {
        this.pollDelay = 4000;
      }

      if (this.anyMfbBuilding && !this.pollTimeoutId && !this.pollInFlight) {
        this.schedulePoll();
      }
    },

    schedulePoll() {
      if (this.pollTimeoutId) {
        clearTimeout(this.pollTimeoutId);
      }

      this.pollTimeoutId = setTimeout(() => this.poll(), this.pollDelay);
    },

    poll() {
      this.pollTimeoutId = null;

      if (document.visibilityState !== 'visible') {
        return;
      }

      if (!this.anyMfbBuilding) {
        return;
      }

      if (!this.hasFreshBuilding()) {
        return;
      }

      if (this.pollInFlight) {
        return;
      }

      this.pollInFlight = true;
      router.reload({
        only: ['payrolls'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          this.pollDelay = Math.min(10000, this.pollDelay + 2000);

          if (this.anyMfbBuilding && document.visibilityState === 'visible') {
            this.schedulePoll();
          }
        },
        onError: () => {
          this.pollDelay = 10000;

          if (this.anyMfbBuilding && document.visibilityState === 'visible') {
            this.schedulePoll();
          }
        },
        onFinish: () => {
          this.pollInFlight = false;
        },
      });
    },

    hasFreshBuilding() {
      const cap = 10 * 60 * 1000;
      const buildingIds = [];
      this.payrolls.data.forEach((p) => {
        (p.categories || []).forEach((c) => {
          if (c.mfb_zip_status === 'building') {
            buildingIds.push(c.id);
          }
        });
      });

      if (buildingIds.length === 0) {
        return false;
      }

      return buildingIds.some((id) => {
        const ts = this.firstSeenBuildingAt[id];

        return !ts || Date.now() - ts < cap;
      });
    },

    onVisibilityChange() {
      if (
        document.visibilityState === 'visible' &&
        this.anyMfbBuilding &&
        !this.pollTimeoutId &&
        !this.pollInFlight
      ) {
        this.pollDelay = 4000;
        this.schedulePoll();
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
  max-height: 3000px;
}
</style>
