<template>
    <div>
        <div class="mt-12 hover:bg-gray-100" :class="isActive('') ? 'bg-indigo-800' : ''" v-if="$page.props.permissions.canViewDashboard">
            <inertia-link class="px-12 py-6 flex items-center group" :href="route('dashboard')">
                <icon name="dashboard" class="w-4 h-4 mr-2"
                      :class="isActive('') ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'"/>
                <div class="font-bold"
                     :class="isActive('') ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'">
                    Dashboard
                </div>
            </inertia-link>
        </div>

        <template v-for="menu in menus">
            <div v-if="menu.subs">
                <div v-if="$page.props.permissions[menu.permission]">
                <div class="hover:bg-gray-100" :class="isActive(menu.name) ? 'bg-indigo-800' : ''" :key="menu.id">
                    <inertia-link class="px-12 py-6 flex items-center group"
                                  href="#" @click="menu.active = !menu.active" preserve-scroll>
                        <icon :name="menu.icon" class="w-4 h-4 mr-2"
                              :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'"/>
                        <div class="font-bold"
                             :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'"
                             v-text="menu.label">
                        </div>

                        <icon v-if="menu.active" name="cheveron-down" class="ml-2"
                              :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'"/>
                        <icon v-else name="cheveron-right" class="ml-2"
                              :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'"/>
                    </inertia-link>
                </div>
                <div>
                    <template v-for="sub in menu.subs">
                        <sub-menu v-if="menu.active && $page.props.permissions[sub.permission]" :url="url" :label="sub.label" :uri="sub.uri" :icon="sub.icon"
                                  :key="sub.id"></sub-menu>
                    </template>
                </div>
                </div>
            </div>

            <div v-else>
                <div class="hover:bg-gray-100" :class="isActive(menu.name) ? 'bg-indigo-800' : ''" :key="menu.id" v-if="$page.props.permissions[menu.permission]">
                    <inertia-link class="px-12 py-6 flex items-center group"
                                  :href="menu.url" @click="menu.active = !menu.active" preserve-scroll>
                        <icon :name="menu.icon" class="w-4 h-4 mr-2"
                              :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'"/>
                        <div class="font-bold"
                             :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'"
                             v-text="menu.label">
                        </div>
                    </inertia-link>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon'
    import SubMenu from '@/Shared/SubMenu'

    export default {

        components: {
            Icon,
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
                        permission: 'canViewSchedule'
                    },
                    analysis: {
                        id: 2,
                        name: 'audit_analysis',
                        label: 'Analysis',
                        url: route('audit_analysis.index'),
                        icon: 'office',
                        active: false,
                        permission: 'canViewAnalysis'
                    },
                    autopay: {
                        id: 3,
                        name: 'audit_autopay',
                        label: 'Autopay',
                        url: route('audit_autopay.index'),
                        icon: 'office',
                        active: false,
                        permission: 'canViewAutopay'
                    },
                    mfb_schedule: {
                        id: 4,
                        name: 'mfb_schedule',
                        label: 'MFB Schedule',
                        url: route('mfb_schedule.index'),
                        icon: 'office',
                        active: false,
                        permission: 'canViewMfbSchedule'
                    },
                    fidelity: {
                        id: 5,
                        name: 'fidelity',
                        label: 'Fidelity',
                        url: route('fidelity.mandate'),
                        icon: 'office',
                        active: false,
                        permission: 'canViewFidelityMandate'
                    },
                    reports: {
                        id: 7, name: 'reports', label: 'Reports', icon: 'printer', active: false, permission: 'canViewReports',
                        subs: {
                            summary: {id: 1, label: 'Payment Summary', uri: this.uri('reports.summary_view'), permission: 'canViewPaymentSummary'},
                            category: {id: 2, label: 'Category Report', uri: this.uri('audit_payroll.index'), permission: 'canViewCategoryReport'},
                            mda: {id: 3, label: 'MDA Report', uri: this.uri('reports.mda_view'), permission: 'canViewMdaReport'},
                            beneficiary: {id: 4, label: 'Beneficiary Report', uri: this.uri('audit_payroll.index'), permission: 'canViewBeneficiaryReport'},
                        }
                    },
                },
            }
        },

        methods: {
            isActive(...urls) {
                if (urls[0] === '') {
                    return this.url === ''
                }

                return urls.filter(url => this.url.startsWith(url)).length
            },

            uri(name) {
                return route(name).urlBuilder.route.uri
            },
        },
    }
</script>
