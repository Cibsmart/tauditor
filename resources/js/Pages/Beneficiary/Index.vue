<template>
  <div>
    <Head title="Beneficiaries" />
    <h1 class="mb-8 text-3xl font-bold">Beneficiaries</h1>
    <div class="mb-6 flex items-center justify-between">
      <!-- Search Filter goes here -->
      <search-filter v-model="form.search" class="mr-4 w-full max-w-lg">
      </search-filter>
      <div></div>
      <Button :as="Link" href="#" size="lg">
        <span class="hidden md:inline">New Beneficiary</span>
      </Button>
    </div>

    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Name</TableHead>
            <TableHead>MDA</TableHead>
            <TableHead>Designation</TableHead>
            <TableHead>Bank Details</TableHead>
            <TableHead>Status</TableHead>
            <TableHead></TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow
            v-for="(beneficiary) in beneficiaries.data"
            :key="beneficiary.id"
          >
            <TableCell>
              <div
                class="text-sm leading-5 font-medium text-gray-900 uppercase"
              >
                {{ beneficiary.name }}
              </div>
              <div class="text-sm leading-5 text-gray-600">
                {{ beneficiary.verification_number }}
              </div>
            </TableCell>
            <TableCell>
              <div class="text-sm leading-5 text-gray-900">
                {{ beneficiary.mda }}
              </div>
              <div class="text-sm leading-5 text-gray-600">
                {{ beneficiary.sub_mda }}
                {{ beneficiary.sub_sub_mda }}
              </div>
            </TableCell>
            <TableCell>
              <div class="text-sm leading-5 text-gray-900">
                {{ beneficiary.designation }}
              </div>
              <div class="text-sm leading-5 text-gray-600">
                {{ beneficiary.step }}
              </div>
            </TableCell>
            <TableCell>
              <div class="text-sm leading-5 text-gray-900">
                {{ beneficiary.account_number }}
              </div>
              <div class="text-sm leading-5 text-gray-600">
                {{ beneficiary.bank_name }}
              </div>
            </TableCell>
            <TableCell>
                            <span
                              :class="
                                    beneficiary.active
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                "
                              class="inline-flex rounded-full px-2 text-xs leading-5 font-semibold"
                            >
                                {{ beneficiary.active ? 'Active' : 'Inactive' }}
                            </span>
            </TableCell>
            <TableCell class="text-right">
              <a
                class="text-indigo-600 hover:text-indigo-900 focus:underline focus:outline-none"
                href="#"
              >Edit</a
              >
            </TableCell>
          </TableRow>

          <TableRow v-if="beneficiaries.data.length === 0">
            <TableCell
              class="text-xs font-medium tracking-wider text-gray-700 uppercase"
              colspan="6"
            >
              No Beneficiary
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <pagination :links="beneficiaries.links" />
  </div>
</template>

<script>
import pickBy from 'lodash/pickBy';
import throttle from 'lodash/throttle';
import { Button } from '@/Components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow, } from '@/Components/ui/table';
import Layout from '@/Shared/Layout';
import Pagination from '@/Shared/Pagination';
import SearchFilter from '@/Shared/SearchFilter';


export default {
  layout: Layout,

  props: {
    beneficiaries: Object,
    filters: Object,
  },

  components: {
    Pagination,
    SearchFilter,
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
      form: {
        search: this.filters.search,
      },
    };
  },

  watch: {
    form: {
      handler: throttle(function () {
        let query = pickBy(this.form);
        this.$inertia.visit(
          this.route(
            'beneficiaries.index',
            Object.keys(query).length
              ? query
              : { remember: 'forget' },
          ),
          { replace: true },
        );
      }, 300),
      deep: true,
    },
  },
};
</script>
