<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  Calendar,
  BarChart2,
  CreditCard,
  Building2,
  Briefcase,
  Users,
  FileText,
  ChevronRight,
} from 'lucide-vue-next';
import {
  CollapsibleRoot,
  CollapsibleContent,
  CollapsibleTrigger,
} from 'reka-ui';
import { ref, computed } from 'vue';
import SidebarMenu from '@/Components/ui/sidebar/SidebarMenu.vue';
import SidebarMenuButton from '@/Components/ui/sidebar/SidebarMenuButton.vue';
import SidebarMenuItem from '@/Components/ui/sidebar/SidebarMenuItem.vue';
import SidebarMenuSub from '@/Components/ui/sidebar/SidebarMenuSub.vue';
import SidebarMenuSubButton from '@/Components/ui/sidebar/SidebarMenuSubButton.vue';
import SidebarMenuSubItem from '@/Components/ui/sidebar/SidebarMenuSubItem.vue';

const page = usePage();
const permissions = computed(() => page.props.permissions);
const currentUrl = computed(() => page.url.replace(/^\//, ''));

const reportsOpen = ref(false);

function isActive(segment) {
  if (segment === '') {
    return currentUrl.value === '';
  }

  return currentUrl.value.startsWith(segment);
}
</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem v-if="permissions.canViewDashboard">
      <SidebarMenuButton
        :as="Link"
        :href="route('dashboard')"
        :is-active="isActive('')"
      >
        <LayoutDashboard class="h-4 w-4 shrink-0" />
        <span>Dashboard</span>
      </SidebarMenuButton>
    </SidebarMenuItem>

    <SidebarMenuItem v-if="permissions.canViewSchedule">
      <SidebarMenuButton
        :as="Link"
        :href="route('audit_payroll.index')"
        :is-active="isActive('audit_payroll')"
      >
        <Calendar class="h-4 w-4 shrink-0" />
        <span>Schedule</span>
      </SidebarMenuButton>
    </SidebarMenuItem>

    <SidebarMenuItem v-if="permissions.canViewAnalysis">
      <SidebarMenuButton
        :as="Link"
        :href="route('audit_analysis.index')"
        :is-active="isActive('audit_analysis')"
      >
        <BarChart2 class="h-4 w-4 shrink-0" />
        <span>Analysis</span>
      </SidebarMenuButton>
    </SidebarMenuItem>

    <SidebarMenuItem v-if="permissions.canViewAutopay">
      <SidebarMenuButton
        :as="Link"
        :href="route('audit_autopay.index')"
        :is-active="isActive('audit_autopay')"
      >
        <CreditCard class="h-4 w-4 shrink-0" />
        <span>Autopay</span>
      </SidebarMenuButton>
    </SidebarMenuItem>

    <SidebarMenuItem v-if="permissions.canViewMfbSchedule">
      <SidebarMenuButton
        :as="Link"
        :href="route('mfb_schedule.index')"
        :is-active="isActive('mfb_schedule')"
      >
        <Building2 class="h-4 w-4 shrink-0" />
        <span>MFB Schedule</span>
      </SidebarMenuButton>
    </SidebarMenuItem>

    <SidebarMenuItem v-if="permissions.canViewFidelityMandate">
      <SidebarMenuButton
        :as="Link"
        :href="route('fidelity.index')"
        :is-active="isActive('fidelity')"
      >
        <Briefcase class="h-4 w-4 shrink-0" />
        <span>Fidelity</span>
      </SidebarMenuButton>
    </SidebarMenuItem>

    <SidebarMenuItem v-if="permissions.canUploadPayeData">
      <SidebarMenuButton
        :as="Link"
        :href="route('paye.index')"
        :is-active="isActive('paye')"
      >
        <Users class="h-4 w-4 shrink-0" />
        <span>Paye Data</span>
      </SidebarMenuButton>
    </SidebarMenuItem>

    <SidebarMenuItem v-if="permissions.canViewReports">
      <CollapsibleRoot v-model:open="reportsOpen">
        <CollapsibleTrigger as-child>
          <SidebarMenuButton :is-active="isActive('reports')">
            <FileText class="h-4 w-4 shrink-0" />
            <span>Reports</span>
            <ChevronRight
              class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200"
              :class="reportsOpen ? 'rotate-90' : ''"
            />
          </SidebarMenuButton>
        </CollapsibleTrigger>
        <CollapsibleContent>
          <SidebarMenuSub>
            <SidebarMenuSubItem v-if="permissions.canViewPaymentSummary">
              <SidebarMenuSubButton
                :as="Link"
                :href="route('reports.summary_view')"
                :is-active="isActive('reports/summary')"
              >
                Payment Summary
              </SidebarMenuSubButton>
            </SidebarMenuSubItem>
            <SidebarMenuSubItem v-if="permissions.canViewMdaReport">
              <SidebarMenuSubButton
                :as="Link"
                :href="route('reports.mda_view')"
                :is-active="isActive('reports/mda')"
              >
                MDA Report
              </SidebarMenuSubButton>
            </SidebarMenuSubItem>
            <SidebarMenuSubItem v-if="permissions.canViewCategoryReport">
              <SidebarMenuSubButton
                :as="Link"
                :href="route('audit_payroll.index')"
                :is-active="false"
              >
                Category Report
              </SidebarMenuSubButton>
            </SidebarMenuSubItem>
            <SidebarMenuSubItem v-if="permissions.canViewBeneficiaryReport">
              <SidebarMenuSubButton
                :as="Link"
                :href="route('audit_payroll.index')"
                :is-active="false"
              >
                Beneficiary Report
              </SidebarMenuSubButton>
            </SidebarMenuSubItem>
          </SidebarMenuSub>
        </CollapsibleContent>
      </CollapsibleRoot>
    </SidebarMenuItem>
  </SidebarMenu>
</template>
