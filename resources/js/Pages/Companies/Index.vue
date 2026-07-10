<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import Pagination from '../../Components/Pagination.vue';

defineProps({
    companies: Object,
});

const destroy = (id) => {
    if (confirm('Delete this company?')) {
        Inertia.delete(`/companies/${id}`);
    }
};
</script>

<template>
    <Head title="Companies" />

    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Companies</h1>
            <Link href="/companies/create" class="btn btn-primary">+ Add Company</Link>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Industry</th>
                            <th>Phone</th>
                            <th>Contacts</th>
                            <th>Owner</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="company in companies.data" :key="company.id">
                            <td class="fw-semibold">{{ company.name }}</td>
                            <td>{{ company.industry || '—' }}</td>
                            <td>{{ company.phone || '—' }}</td>
                            <td>{{ company.contacts_count }}</td>
                            <td>{{ company.owner ? company.owner.name : '—' }}</td>
                            <td class="text-end">
                                <Link :href="`/companies/${company.id}/edit`" class="btn btn-sm btn-outline-secondary me-1">Edit</Link>
                                <button @click="destroy(company.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="companies.data.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">No companies yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <Pagination :links="companies.links" />
        </div>
    </AuthenticatedLayout>
</template>
