<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { ChevronsUpDown, LogOut, UserRound } from 'lucide-vue-next';
import Sidebar from '@/Components/ui/sidebar/Sidebar.vue';
import SidebarHeader from '@/Components/ui/sidebar/SidebarHeader.vue';
import SidebarContent from '@/Components/ui/sidebar/SidebarContent.vue';
import SidebarFooter from '@/Components/ui/sidebar/SidebarFooter.vue';
import SidebarGroup from '@/Components/ui/sidebar/SidebarGroup.vue';
import SidebarGroupContent from '@/Components/ui/sidebar/SidebarGroupContent.vue';
import SidebarMenu from '@/Components/ui/sidebar/SidebarMenu.vue';
import SidebarMenuItem from '@/Components/ui/sidebar/SidebarMenuItem.vue';
import SidebarMenuButton from '@/Components/ui/sidebar/SidebarMenuButton.vue';
import NavMain from '@/Components/NavMain.vue';
import Logo from '@/Shared/Logo.vue';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuLabel,
} from '@/Components/ui/dropdown-menu';

const page = usePage();
const user = computed(() => page.props.auth.user);
const permissions = computed(() => page.props.permissions);
</script>

<template>
    <Sidebar>
        <!-- Header: logo + app name -->
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton :as="Link" href="/" class="h-10">
                        <Logo class="h-5 w-5 shrink-0 text-primary" />
                        <span class="truncate text-sm font-semibold"
                            >TAuditor</span
                        >
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Content: navigation -->
        <SidebarContent>
            <SidebarGroup>
                <SidebarGroupContent>
                    <NavMain />
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <!-- Footer: user account dropdown -->
        <SidebarFooter>
            <SidebarMenu>
                <SidebarMenuItem>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <SidebarMenuButton class="h-12">
                                <div
                                    class="flex min-w-0 flex-col text-left leading-tight"
                                >
                                    <span
                                        class="truncate text-sm font-semibold"
                                    >
                                        {{ user.first_name }}
                                        {{ user.last_name }}
                                    </span>
                                    <span
                                        class="truncate text-xs text-sidebar-foreground/60"
                                    >
                                        {{ user.domain.name }}
                                    </span>
                                </div>
                                <ChevronsUpDown
                                    class="ml-auto h-4 w-4 shrink-0 text-sidebar-foreground/50"
                                />
                            </SidebarMenuButton>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            side="top"
                            align="start"
                            class="w-56"
                        >
                            <DropdownMenuLabel class="font-normal">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-semibold"
                                        >{{ user.first_name }}
                                        {{ user.last_name }}</span
                                    >
                                    <span
                                        class="text-xs text-muted-foreground"
                                        >{{ user.domain.name }}</span
                                    >
                                </div>
                            </DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem as-child>
                                <Link
                                    href="#"
                                    class="flex cursor-pointer items-center gap-2"
                                >
                                    <UserRound class="h-4 w-4" />
                                    My Profile
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem
                                v-if="permissions.canViewUsers"
                                as-child
                            >
                                <Link
                                    :href="route('manage_users.index')"
                                    class="flex cursor-pointer items-center gap-2"
                                >
                                    Manage Users
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem as-child>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="flex w-full cursor-pointer items-center gap-2"
                                >
                                    <LogOut class="h-4 w-4" />
                                    Log out
                                </Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>
    </Sidebar>
</template>
