<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import Pagination from '../../Components/Pagination.vue';

defineProps({
    contacts: Object,
});

const destroy = (id) => {
    if (confirm('Delete this contact?')) {
        Inertia.delete(`/contacts/${id}`);
    }
};
</script>

<template>
    <Head title="Contacts" />

    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Contacts</h1>
            <Link href="/contacts/create" class="btn btn-primary">+ Add Contact</Link>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contact in contacts.data" :key="contact.id">
                            <td class="fw-semibold">{{ contact.first_name }} {{ contact.last_name }}</td>
                            <td>{{ contact.email || '—' }}</td>
                            <td>{{ contact.phone || '—' }}</td>
                            <td>{{ contact.job_title || '—' }}</td>
                            <td>{{ contact.company ? contact.company.name : '—' }}</td>
                            <td class="text-end">
                                <Link :href="`/contacts/${contact.id}/edit`" class="btn btn-sm btn-outline-secondary me-1">Edit</Link>
                                <button @click="destroy(contact.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="contacts.data.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">No contacts yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <Pagination :links="contacts.links" />
        </div>
    </AuthenticatedLayout>
</template>
