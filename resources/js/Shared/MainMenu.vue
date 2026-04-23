<template>
  <div>
    <div
      v-if="$page.props.permissions.canViewDashboard"
      :class="isActive('') ? 'bg-indigo-800' : ''"
      class="mt-12 hover:bg-gray-100"
    >
      <Link
        :href="route('dashboard')"
        class="group flex items-center px-12 py-6"
      >
        <icon
          :class="
                        isActive('')
                            ? 'text-white group-hover:text-orange-800'
                            : 'text-indigo-800 group-hover:text-orange-800'
                    "
          class="mr-2 h-4 w-4"
          name="dashboard"
        />
        <div
          :class="
                        isActive('')
                            ? 'text-white group-hover:text-orange-800'
                            : 'text-indigo-800 group-hover:text-orange-800'
                    "
          class="font-bold"
        >
          Dashboard
        </div>
      </Link>
    </div>

    <template v-for="menu in menus">
      <div v-if="menu.subs" :key="menu.id">
        <div v-if="$page.props.permissions[menu.permission]">
          <div
            :key="menu.id"
            :class="isActive(menu.name) ? 'bg-indigo-800' : ''"
            class="hover:bg-gray-100"
          >
            <Link
              class="group flex items-center px-12 py-6"
              href="#"
              preserve-scroll
              @click="menu.active = !menu.active"
            >
              <icon
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                :name="menu.icon"
                class="mr-2 h-4 w-4"
              />
              <div
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                class="font-bold"
                v-text="menu.label"
              ></div>

              <icon
                v-if="menu.active"
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                class="ml-2"
                name="cheveron-down"
              />
              <icon
                v-else
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                class="ml-2"
                name="cheveron-right"
              />
            </Link>
          </div>
          <div>
            <template v-for="sub in menu.subs">
              <sub-menu
                v-if="
                                    menu.active &&
                                    $page.props.permissions[sub.permission]
                                "
                :key="sub.id"
                :icon="sub.icon"
                :label="sub.label"
                :uri="sub.uri"
                :url="url"
              ></sub-menu>
            </template>
          </div>
        </div>
        <div v-else>
          <div
            :key="menu.id"
            :class="isActive(menu.name) ? 'bg-indigo-800' : ''"
            class="hover:bg-gray-100"
          >
            <Link
              class="group flex items-center px-12 py-6"
              href="#"
              preserve-scroll
              @click="menu.active = !menu.active"
            >
              <icon
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                :name="menu.icon"
                class="mr-2 h-4 w-4"
              />
              <div
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                class="font-bold"
                v-text="menu.label"
              ></div>

              <icon
                v-if="menu.active"
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                class="ml-2"
                name="cheveron-down"
              />
              <icon
                v-else
                :class="
                                    isActive(menu.name)
                                        ? 'text-white group-hover:text-orange-800'
                                        : 'text-indigo-800 group-hover:text-orange-800'
                                "
                class="ml-2"
                name="cheveron-right"
              />
            </Link>
          </div>
          <div>
            <template v-for="sub in menu.subs">
              <sub-menu
                v-if="
                                    menu.active &&
                                    $page.props.permissions[sub.permission]
                                "
                :key="sub.id"
                :icon="sub.icon"
                :label="sub.label"
                :uri="sub.uri"
                :url="url"
              ></sub-menu>
            </template>
          </div>
        </div>
      </div>

      <template v-else>
        <div
          :key="menu.id"
          :class="isActive(menu.name) ? 'bg-indigo-800' : ''"
          class="hover:bg-gray-100"
        >
          <Link
            :href="menu.url"
            class="group flex items-center px-12 py-6"
            preserve-scroll
            @click="menu.active = !menu.active"
          >
            <icon
              :class="
                                isActive(menu.name)
                                    ? 'text-white group-hover:text-orange-800'
                                    : 'text-indigo-800 group-hover:text-orange-800'
                            "
              :name="menu.icon"
              class="mr-2 h-4 w-4"
            />
            <div
              :class="
                                isActive(menu.name)
                                    ? 'text-white group-hover:text-orange-800'
                                    : 'text-indigo-800 group-hover:text-orange-800'
                            "
              class="font-bold"
              v-text="menu.label"
            ></div>
          </Link>
        </div>
      </template>
    </template>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Shared/Icon';
import SubMenu from '@/Shared/SubMenu';

export default {
  components: {
    Icon,
    Link,
    SubMenu,
  },

  props: {
    url: String,
  },

  data() {
    return {
      menus: {
        schedule: {
          id: 1,
          name: 'audit_payroll',
          label: 'Schedule',
          url: route('audit_payroll.index'),
          icon: 'office',
          active: false,
          permission: 'canViewSchedule',
        },
        analysis: {
          id: 2,
          name: 'audit_analysis',
          label: 'Analysis',
          url: route('audit_analysis.index'),
          icon: 'location',
          active: false,
          permission: 'canViewAnalysis',
        },
        autopay: {
          id: 3,
          name: 'audit_autopay',
          label: 'Autopay',
          url: route('audit_autopay.index'),
          icon: 'shopping-cart',
          active: false,
          permission: 'canViewAutopay',
        },
        mfb_schedule: {
          id: 4,
          name: 'mfb_schedule',
          label: 'MFB Schedule',
          url: route('mfb_schedule.index'),
          icon: 'list',
          active: false,
          permission: 'canViewMfbSchedule',
        },
        fidelity: {
          id: 5,
          name: 'fidelity',
          label: 'Fidelity',
          url: route('fidelity.index'),
          icon: 'brief-case',
          active: false,
          permission: 'canViewFidelityMandate',
        },
        paye: {
          id: 6,
          name: 'paye',
          label: 'Paye Data',
          url: route('paye.index'),
          icon: 'users',
          active: false,
          permission: 'canUploadPayeData',
        },
        reports: {
          id: 7,
          name: 'reports',
          label: 'Reports',
          icon: 'printer',
          active: false,
          permission: 'canViewReports',
          subs: {
            summary: {
              id: 1,
              label: 'Payment Summary',
              uri: this.uri('reports.summary_view'),
              permission: 'canViewPaymentSummary',
            },
            category: {
              id: 2,
              label: 'Category Report',
              uri: this.uri('audit_payroll.index'),
              permission: 'canViewCategoryReport',
            },
            mda: {
              id: 3,
              label: 'MDA Report',
              uri: this.uri('reports.mda_view'),
              permission: 'canViewMdaReport',
            },
            beneficiary: {
              id: 4,
              label: 'Beneficiary Report',
              uri: this.uri('audit_payroll.index'),
              permission: 'canViewBeneficiaryReport',
            },
          },
        },
      },
    };
  },

  methods: {
    isActive(...urls) {
      if (urls[0] === '') {
        return this.url === '';
      }

      return urls.filter((url) => this.url.startsWith(url)).length;
    },

    uri(name) {
      return route(name, {}, false).toString().replace(/^\//, '');
    },
  },
};
</script>
