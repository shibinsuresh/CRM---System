<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import Pagination from '../../Components/Pagination.vue';

defineProps({
    users: Object,
    currentUserId: Number,
});

const roleBadge = (role) => ({
    admin: 'bg-danger',
    manager: 'bg-primary',
    sales_rep: 'bg-secondary',
}[role] || 'bg-secondary');

const destroy = (id) => {
    if (confirm('Delete this user?')) {
        Inertia.delete(`/users/${id}`);
    }
};
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Users</h1>
            <Link href="/users/create" class="btn btn-primary">+ Add User</Link>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="fw-semibold">
                                {{ user.name }}
                                <span v-if="user.id === currentUserId" class="badge bg-light text-dark ms-1">you</span>
                            </td>
                            <td>{{ user.email }}</td>
                            <td><span class="badge text-capitalize" :class="roleBadge(user.role)">{{ user.role.replace('_', ' ') }}</span></td>
                            <td class="text-end">
                                <Link :href="`/users/${user.id}/edit`" class="btn btn-sm btn-outline-secondary me-1">Edit</Link>
                                <button
                                    v-if="user.id !== currentUserId"
                                    @click="destroy(user.id)"
                                    class="btn btn-sm btn-outline-danger"
                                >Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <Pagination :links="users.links" />
        </div>
    </AuthenticatedLayout>
</template>
