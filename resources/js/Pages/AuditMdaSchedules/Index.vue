<template>
  <div>
    <Head title="Audit MDA Schedules" />
    <h1 class="mb-4 font-bold text-3xl">
      <Link :href="route('audit_payroll.index')">
        Audit Payroll
      </Link>
      <span class=" font-medium">/</span> MDA Schedules
    </h1>

    <div class="mb-6 flex justify-between items-center">
    </div>

    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>MDA</TableHead>
            <TableHead>Month</TableHead>
            <TableHead>Total Amount</TableHead>
            <TableHead>Uploaded</TableHead>
            <TableHead class="text-right"></TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="schedule in schedules.data" :key="schedule.id">
            <TableCell>
              <div class="flex items-center">
                <div class="ml-4">
                  <div class="text-sm leading-5 font-medium  uppercase">
                    {{ schedule.mda_name }}
                    <span :class="schedule.pension ? 'bg-pink-100 text-pink-800' : ''"
                          class="px-2 text-xs leading-5 font-semibold rounded-full">
                                            {{ schedule.pension ? 'Pension' : '' }}
                                        </span>
                  </div>
                  <div class="text-sm leading-5 text-muted-foreground">{{ schedule.domain }}</div>
                </div>
              </div>
            </TableCell>
            <TableCell>
              <div class="text-sm leading-5 ">
                {{ schedule.month }}
              </div>
              <div class="text-sm leading-5 text-muted-foreground">
                {{ schedule.year }}
              </div>
            </TableCell>
            <TableCell>
              <div class="text-sm leading-5 ">
                <span class="line-through">N</span>
                {{ schedule.total_amount }}
              </div>
              <div class="text-sm leading-5 text-muted-foreground">
                <span>Head Count: </span>
                {{ schedule.head_count }}
              </div>
            </TableCell>

            <TableCell>
                            <span :class="schedule.uploaded
                                  ? 'bg-green-100 text-green-800'
                                  : 'bg-red-100 text-red-800'"
                                  class="px-2 inline-flex text-xs leading-5 uppercase font-semibold rounded-full">
                                {{ schedule.uploaded ? 'uploaded' : 'pending' }}
                            </span>
            </TableCell>

            <TableCell class="text-right">
              <form v-show="schedule.uploaded && ! schedule.has_sub && ! schedule.archived"
                    :key="schedule.id + schedule.sub_mda_id"
                    class="inline"
                    @submit.prevent="reupload(schedule.sub_mda_id, schedule.mda_name)">
                <Button size="sm" type="submit" variant="ghost">
                  Re-upload
                </Button>
              </form>

              <!--  View Sub MDA Details for MDA with Sub MDAs -->
              <Link v-if="schedule.uploaded && schedule.has_sub"
                    :href="route('audit_sub_mda_schedules.index', {audit_mda_schedule: schedule.id})"
                    class="px-5 py-3">
                View Details
              </Link>

              <Link v-else-if="schedule.uploaded"
                    :href="route('audit_pay_schedules.index', {audit_sub_mda_schedule: schedule.sub_mda_id})"
                    class="px-5 py-3">
                View Details
              </Link>

              <Link v-else-if="schedule.has_sub"
                    :href="route('audit_sub_mda_schedules.index', {audit_mda_schedule: schedule.id})"
                    class="px-5 py-3">
                Upload
              </Link>

              <form v-else :key="schedule.id" @submit.prevent="upload(schedule.sub_mda_id)">
                <div class="flex items-center">
                  <file-input v-model="form.schedule_file[schedule.sub_mda_id]"
                              :errors="form.errors.schedule_file" accept="file/*" class="pr-6 w-full"
                              type="file" />
                  <Button size="sm" type="submit">
                    Upload
                  </Button>
                </div>
              </form>
            </TableCell>
          </TableRow>

          <TableRow v-if="schedules.data.length === 0">
            <TableCell class="text-xs font-medium uppercase tracking-wider" colspan="6">
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
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import FileInput from "@/Shared/FileInput";
import Pagination from '@/Shared/Pagination'
import { Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'

export default {
  layout: Layout,

  props: {
    schedules: Object,
  },

  components: {
    Icon,
    Link,
    FileInput,
    Pagination,
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
      schedule_file: [],
    })
    return { form }
  },

  methods: {
    upload(audit_sub_mda) {
      this.form
        .transform((data) => ({
          audit_sub_mda: audit_sub_mda ? audit_sub_mda : '',
          schedule_file: data.schedule_file[audit_sub_mda]
        }))
        .post('/audit_pay_schedules/store', {
          preserveScroll: true
        })
    },

    reupload(audit_sub_mda, mda_name) {

      let result = confirm('Confirm Re-Upload for' + mda_name);

      if (result) {
        this.form
          .post(`/audit_pay_schedules/${audit_sub_mda}/destroy`, {
            preserveScroll: true,
          })
      }
    }
  }
}
</script>
