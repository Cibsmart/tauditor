<template>
    <div>
      <div class="mb-4">
        <inertia-link class="py-3 flex items-center group" :href="route('dashboard')">
          <icon name="dashboard" class="w-4 h-4 mr-2" 
              :class="isActive('') ? 'fill-current text-white' : 'fill-current text-indigo-300 hover:fill-current group-hover:text-white'" />
          <div :class="isActive('') ? 'text-white' : 'text-indigo-300 group-hover:text-white' ">
            Dashboard
          </div>
        </inertia-link>
      </div>

      <div class="mb-4">
        <inertia-link class="py-3 flex items-center group" href="#" >
          <icon name="users" class="w-4 h-4 mr-2" 
              :class="isActive('#') ? 'fill-current text-white' : 'fill-current text-indigo-300 group-hover:fill-current group-hover:text-white'" />
          <div :class="isActive('#') ? 'text-white' : 'text-indigo-300 group-hover:text-white' ">
            Employees
          </div>
        </inertia-link>
      </div>

      <div class="mb-4">
        <inertia-link class="py-3 flex items-center group" href="#">
          <icon name="office" class="w-4 h-4 mr-2" 
              :class="isActive('#') ? 'fill-current text-white' : 'fill-current text-indigo-300 group-hover:fill-current group-hover:text-white'" />
          <div :class="isActive('#') ? 'text-white' : 'text-indigo-300 group-hover:text-white' ">
            Payroll
          </div>
        </inertia-link>
      </div>

      <div class="mb-4">
        <inertia-link class="py-3 flex items-center group" href="#">
          <icon name="store-front" class="w-4 h-4 mr-2" 
              :class="isActive('#') ? 'fill-current text-white' : 'fill-current text-indigo-300 group-hover:fill-current group-hover:text-white'" />
          <div :class="isActive('#') ? 'text-white' : 'text-indigo-300 group-hover:text-white' ">
            Setup
          </div>
        </inertia-link>
      </div>

      <div class="mb-4">
        <inertia-link class="py-3 flex items-center group" href="#">
          <icon name="printer" class="w-4 h-4 mr-2" 
              :class="isActive('#') ? 'fill-current text-white' : 'fill-current text-indigo-300 group-hover:fill-current group-hover:text-white'" />
          <div :class="isActive('#') ? 'text-white' : 'text-indigo-300 group-hover:text-white' ">
            Reports
          </div>
        </inertia-link>
      </div>

      <div class="mb-4">
        <inertia-link class="py-3 flex items-center group" href="#" @click="activate('audit')" preserve-scroll>
          <icon name="printer" class="w-4 h-4 mr-2" 
              :class="isActive('#') ? 'fill-current text-white' : 'fill-current text-indigo-300 group-hover:fill-current group-hover:text-white'" />
          <div :class="isActive('#') ? 'text-white' : 'text-indigo-300 group-hover:text-white' ">
            Audit System
          </div>
        </inertia-link>

        <!-- How to add sub-menus -->
        <div v-if="menus.audit" class="ml-4 mt-2">
          <sub-menu :url="url" label="Schedules"></sub-menu>
          <sub-menu :url="url" label="Upload Schedule"></sub-menu>
        </div>
      </div>
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
        dashboard: false,
        employees: false,
        payroll: false,
        setup: false,
        reports: false,
        audit: false,
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

    activate(menu){
      for(let key in this.menus ){
        if(this.menus[key] != this.menus[menu]){
          this.menus[key] = false
        }
      }
      this.menus[menu] = ! this.menus[menu]
    },

    uri(name){
      return route(name).urlBuilder.route.uri
    },
  },
}
</script>