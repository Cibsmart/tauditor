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
                      <div class="whitespace-nowrap">
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
                            class="px-5 py-3"
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
                            class="px-5 py-3"
                            preserve-scroll
                            preserve-state
                          >
                            View MDAs
                          </Link>
                        </Button>
                      </div>
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
                      <Button v-show="category.can_generate" asChild size="sm">
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

                      <Link
                        v-show="category.refreshable"
                        :href="route('audit_autopay.index')"
                        preserve-scroll
                        preserve-state
                      >
                        Refresh
                      </Link>

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
import { Link, router } from '@inertiajs/vue3';
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
