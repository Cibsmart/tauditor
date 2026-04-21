<template>
    <div>
        <Head title="Users" />
        <h1 class="mb-8 font-bold text-3xl">Users</h1>

        <div>
            <select-input v-model="form.role" class="pb-8 w-full"
                          label="Payroll Month"
                          @update:modelValue="roleChanged">
                <option disabled value="" class="text-gray-100">Select Role</option>
                <option v-for="role in roles" :key="role.id" :value="role.id">
                    {{ role.name }}
                </option>
            </select-input>
        </div>

        <div>
            <div class="mb-6 flex justify-between items-center" v-if="can.create_user" >
                <div></div>
                <Button as="a" :href="route('manage_users.create')" size="lg">
                    Create<span class="hidden md:inline">&nbsp; User</span>
                </Button>
            </div>

            <div class="flex flex-col">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white">
                            <tr v-for="user in users.data" :key="user.id">
                                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{ user.name }}</div>
                                </td>

                                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 font-medium text-gray-900 lowercase">{{ user.email }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                  <span class="px-2 bg-green-100 text-green-800 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ user.role }}
                                  </span>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                    <a href="#" class="text-red-600 hover:text-red-900 focus:outline-none focus:underline">Delete</a>
                                </td>
                            </tr>
                            <tr v-if="users.data && users.data.length === 0">
                                <td colspan="5" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    No User in Role
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <pagination :links="users.links" />
        </div>

        <div v-show="new_users.length > 0">
            <h1 class="mt-8 mb-2 font-bold text-2xl">New Users</h1>
            <div class="flex flex-col">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                        <table class="min-w-full">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Email Sent
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white">
                            <tr v-for="user in new_users" :key="user.id">
                                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 font-medium text-gray-900 uppercase">{{ user.name }}</div>
                                </td>

                                <td class="px-6 py-2 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 font-medium text-gray-900 lowercase">{{ user.email }}</div>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 font-light text-gray-900 uppercase">
                                    {{ user.role }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                    <div class="text-sm leading-5 font-light text-gray-900 lowercase">
                                        {{ user.email_sent }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                  <span class="px-2 bg-red-100 text-red-800 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ user.status }}
                                  </span>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 focus:outline-none focus:underline">Edit</a>
                                </td>
                            </tr>
                            <tr v-if="new_users && new_users.length === 0">
                                <td colspan="5" class="px-6 py-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-700 uppercase tracking-wider">
                                    No New User in Role
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <pagination :links="new_users.links" />
        </div>
    </div>
</template>

<script>
    import Icon from '@/Shared/Icon';
    import Layout from '@/Shared/Layout';
    import { router } from '@inertiajs/vue3'
    import Pagination from '@/Shared/Pagination';
    import SelectInput from "@/Shared/SelectInput";
    import { Button } from '@/Components/ui/button';

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
        },

        data() {
            return {
                form: {
                    role: '',
                },
            }
        },

        methods: {

            roleChanged() {
                router.reload({data: this.form, only: ['users', 'new_users', 'role']})
            },
        },
    }
</script>

