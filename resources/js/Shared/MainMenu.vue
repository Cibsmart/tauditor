<template>
    <div>
        <div class="mt-12 hover:bg-gray-100" :class="isActive('') ? 'bg-indigo-800' : ''">
            <inertia-link class="px-12 py-6 flex items-center group" :href="route('dashboard')">
                <icon name="dashboard" class="w-4 h-4 mr-2"
                      :class="isActive('') ? 'text-white' : 'text-indigo-800 group-hover:text-orange-800'" />
                <div class="font-bold" :class="isActive('') ? 'text-white' : 'text-indigo-800 group-hover:text-orange-800'">
                    Dashboard
                </div>
            </inertia-link>
        </div>

      <template v-for="menu in menus" >
        <div class="hover:bg-gray-100" :class="isActive(menu.name) ? 'bg-indigo-800' : ''" :key="menu.id">
            <inertia-link class="px-12 py-6 flex items-center group"
                          href="#" @click="menu.active = !menu.active" preserve-scroll>
                <icon :name="menu.icon" class="w-4 h-4 mr-2"
                      :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'" />
                <div class="font-bold" :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'" v-text="menu.label">
                </div>

                <icon v-if="menu.active" name="cheveron-down" class="ml-2"
                      :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'" />
                <icon v-else name="cheveron-right" class="ml-2"
                      :class="isActive(menu.name) ? 'text-white group-hover:text-orange-800' : 'text-indigo-800 group-hover:text-orange-800'" />
            </inertia-link>
        </div>
        <div>
            <template v-for="sub in menu.subs">
                <sub-menu v-if="menu.active" :url="url" :label="sub.label" :uri="sub.uri" :icon="sub.icon" :key="sub.id"></sub-menu>
            </template>
        </div>
      </template>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import SubMenu from '@/Shared/SubMenu'

export default{

  components: {
    Icon,
    SubMenu,
  },

  props: {
    url: String,
  },

  data(){
    return{
      menus: {
          // dashboard : { id: 1, name: '',  label: 'Dashboard', url: route('dashboard'), icon: 'dashboard', active: false },
          beneficiaries : { id: 2, name: 'beneficiaries',  label: 'Beneficiaries', icon: 'users', active: false,
              subs: {
                    index: { id: 1, label: 'Beneficiaries', uri: this.uri('beneficiaries.index'), },
                    create: { id: 2, label: 'New Beneficiary', uri: this.uri('beneficiaries.create'), }
              }
          },
          allowances: { id: 3, name: 'allowances',  label: 'Allowances', icon: 'shopping-cart', active: false,
              subs: {
                  index: { id: 1, label: 'Allowances', uri: this.uri('allowances.index'), },
              }
          },
          deductions: { id: 4, name: 'deductions',  label: 'Deductions', icon: 'trash', active: false,
              subs: {
                  index: { id: 1, label: 'Deductions', uri: this.uri('deductions.index'), },
              }
          },
          payroll: { id: 5, name: 'payroll',  label: 'Payroll', icon: 'office', active: false,
              subs: {
                  index: { id: 1, label: 'Payroll', uri: this.uri('payroll.index'), },
              }
          },
          setup: { id: 6, name: 'setup',  label: 'Setup', icon: 'store-front', active: false },
          reports: { id: 7, name: 'reports',  label: 'Reports', icon: 'printer', active: false },
          audit: { id: 8, name: 'audit',  label: 'Audit System', icon: 'brief-case', active: false },
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

    uri(name){
      return route(name).urlBuilder.route.uri
    },
  },
}
</script>
