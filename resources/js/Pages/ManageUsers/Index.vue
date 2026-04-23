<template>
    <div>
        <Head title="Users" />
        <h1 class="mb-8 text-3xl font-bold">Users</h1>

        <div>
            <select-input
                v-model="form.role"
                class="w-full pb-8"
                label="Payroll Month"
                @update:modelValue="roleChanged"
            >
                <option disabled value="" class="text-gray-100">
                    Select Role
                </option>
                <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.name }}
                </option>
            </select-input>
        </div>

        <div>
            <div
                class="mb-6 flex items-center justify-between"
                v-if="can.create_user"
            >
                <div></div>
                <Button as="a" :href="route('manage_users.create')" size="lg">
                    Create<span class="hidden md:inline">&nbsp; User</span>
                </Button>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Role</TableHead>
                            <TableHead></TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium text-gray-900 uppercase"
                                >
                                    {{ user.name }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium text-gray-900 lowercase"
                                >
                                    {{ user.email }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex rounded-full bg-green-100 px-2 text-xs leading-5 font-semibold text-green-800"
                                >
                                    {{ user.role }}
                                </span>
                            </TableCell>
                            <TableCell class="text-right">
                                <a
                                    href="#"
                                    class="text-indigo-600 hover:text-indigo-900 focus:underline focus:outline-none"
                                    >Edit</a
                                >
                            </TableCell>
                            <TableCell class="text-right">
                                <a
                                    href="#"
                                    class="text-red-600 hover:text-red-900 focus:underline focus:outline-none"
                                    >Delete</a
                                >
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="users.data && users.data.length === 0">
                            <TableCell
                                colspan="5"
                                class="text-xs font-medium tracking-wider text-gray-700 uppercase"
                            >
                                No User in Role
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <pagination :links="users.links" />
        </div>

        <div v-show="new_users.length > 0">
            <h1 class="mt-8 mb-2 text-2xl font-bold">New Users</h1>
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Role</TableHead>
                            <TableHead>Email Sent</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in new_users" :key="user.id">
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium text-gray-900 uppercase"
                                >
                                    {{ user.name }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-medium text-gray-900 lowercase"
                                >
                                    {{ user.email }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-light text-gray-900 uppercase"
                                >
                                    {{ user.role }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <div
                                    class="text-sm leading-5 font-light text-gray-900 lowercase"
                                >
                                    {{ user.email_sent }}
                                </div>
                            </TableCell>
                            <TableCell>
                                <span
                                    class="inline-flex rounded-full bg-red-100 px-2 text-xs leading-5 font-semibold text-red-800"
                                >
                                    {{ user.status }}
                                </span>
                            </TableCell>
                            <TableCell class="text-right">
                                <a
                                    href="#"
                                    class="text-indigo-600 hover:text-indigo-900 focus:underline focus:outline-none"
                                    >Edit</a
                                >
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="new_users && new_users.length === 0">
                            <TableCell
                                colspan="5"
                                class="text-xs font-medium tracking-wider text-gray-700 uppercase"
                            >
                                No New User in Role
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <pagination :links="new_users.links" />
        </div>
    </div>
</template>

<script>
import Icon from '@/Shared/Icon';
import Layout from '@/Shared/Layout';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Shared/Pagination';
import SelectInput from '@/Shared/SelectInput';
import { Button } from '@/Components/ui/button';
import {
    Table,
    TableHeader,
    TableBody,
    TableRow,
    TableHead,
    TableCell,
} from '@/Components/ui/table';

export default {
    layout: Layout,

    props: {
        can: Object,
        roles: Array,
        users: Object,
        new_users: Array,
    },

    components: {
        Icon,
        Pagination,
        SelectInput,
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
                role: '',
            },
        };
    },

    methods: {
        roleChanged() {
            router.reload({
                data: this.form,
                only: ['users', 'new_users', 'role'],
            });
        },
    },
};
</script>
