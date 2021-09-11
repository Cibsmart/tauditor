<template>
    <div>
        <portal-target name="dropdown" slim/>
        <div class="flex flex-col">
            <div class="h-screen flex flex-col" @click="hideDropdownMenus">
                <!-- Uppder Part of the Screen containing Branding, Header and Profile -->
                <div class="md:flex">
                    <!-- Branding and Main Menu for Mobile Screen -->
                    <div
                        class="bg-gray-100 md:flex-shrink-0 md:w-64 py-4 flex items-center justify-between md:justify-center">
                        <!-- Logo -->
                        <inertia-link class="mt-1" href="/">
                            <logo class="text-indigo-900" width="120" height="28"/>
                        </inertia-link>
                        <!-- Menu for Mobile Screen Visible only on Mobile screen -->
                        <dropdown class="md:hidden">
                            <icon name="list" class="text-indigo-800 w-6 h-6"/>
                            <div slot="dropdown" class="mt-2 shadow-lg bg-white rounded">
                                <main-menu :url="url()"/>
                            </div>
                        </dropdown>
                    </div>

                    <!-- Header and Profile with Dropdown -->
                    <div
                        class="bg-white border-b w-full p-4 md:py-0 md:px-12 text-sm md:text-base flex justify-between items-center">
                        <!-- Header -->
                        <div class="mt-1 mr-4">{{ $page.props.auth.user.domain.name }}</div>

                        <!-- Profile with Dropdown -->
                        <dropdown class="mt-1">
                            <div class="flex items-center cursor-pointer select-none">
                                <div class="text-gray-900 focus:text-indigo-800 mr-1 whitespace-no-wrap">
                                    <span>{{ $page.props.auth.user.first_name }}</span>
                                    <span class="hidden md:inline">{{ $page.props.auth.user.last_name }}</span>
                                </div>
                                <icon
                                    class="w-5 h-5 fill-current text-gray-900 focus:fill-current focus:text-indigo-800"
                                    name="cheveron-down"/>
                            </div>
                            <div slot="dropdown" class="mt-2 py-2 shadow-lg bg-white rounded text-sm">
                                <inertia-link class="block px-6 py-2 hover:bg-indigo-800 hover:text-white" href="#">My
                                    Profile
                                </inertia-link>
                                <inertia-link class="block px-6 py-2 hover:bg-indigo-800 hover:text-white"
                                              :href="route('manage_users.index')"
                                              v-if="$page.props.permissions.canViewUsers">
                                    Manage Users
                                </inertia-link>
                                <inertia-link class="block px-6 py-2 hover:bg-indigo-800 hover:text-white"
                                              :href="route('logout')" method="post" as="button">
                                    Logout
                                </inertia-link>
                            </div>
                        </dropdown>
                    </div>
                </div>

                <!-- Lower part of the screen -->
                <div class="flex flex-grow overflow-hidden">
                    <!-- Main Menu on the Left Side Bar Visible on Medium Screen-->
                    <main-menu :url="url()" class="bg-white flex-shrink-0 w-64 hidden md:block overflow-y-auto"/>

                    <!-- Display Screen on the Right of the Main Menu becomes Full screen on sm -->
                    <div class="w-full overflow-hidden px-4 py-8 md:p-12 overflow-y-auto" scroll-region>
                        <flash-messages/>
                        <slot/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import Logo from '@/Shared/Logo'
import Icon from '@/Shared/Icon'
import Dropdown from '@/Shared/Dropdown'
import MainMenu from '@/Shared/MainMenu'
import FlashMessages from '@/Shared/FlashMessages'

export default {
    components: {
        Icon,
        Logo,
        MainMenu,
        Dropdown,
        FlashMessages,
    },

    data() {
        return {
            showUserMenu: false,
        }
    },

    // mounted() {
    //     console.log(this.user)
    // },
    //
    // computed: {
    //     user() {
    //         return this.$page.props.auth.user
    //     }
    // },

    methods: {
        url() {
            return location.pathname.substr(1)
        },

        hideDropdownMenus() {
            this.showUserMenu = false
        },
    },
}


</script>
