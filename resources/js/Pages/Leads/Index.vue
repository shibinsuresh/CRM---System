<script setup>
import { ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import Pagination from '../../Components/Pagination.vue';

const props = defineProps({
    leads: Object,
    filters: Object,
    statuses: Array,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = debounce(() => {
    Inertia.get('/leads', { search: search.value, status: status.value }, { preserveState: true, replace: true });
}, 300);

watch([search, status], applyFilters);

const badgeClass = (status) => ({
    new: 'bg-secondary',
    contacted: 'bg-info',
    qualified: 'bg-primary',
    lost: 'bg-danger',
    converted: 'bg-success',
}[status] || 'bg-secondary');

const destroy = (id) => {
    if (confirm('Delete this lead?')) {
        Inertia.delete(`/leads/${id}`);
    }
};

const convert = (id) => {
    if (confirm('Convert this lead into a contact?')) {
        Inertia.post(`/leads/${id}/convert`);
    }
};
</script>

<template>
    <Head title="Leads" />

    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Leads</h1>
            <Link href="/leads/create" class="btn btn-primary">+ Add Lead</Link>
        </div>

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <input v-model="search" type="search" class="form-control" style="max-width: 320px;" placeholder="Search leads…" />
            <select v-model="status" class="form-select text-capitalize" style="max-width: 200px;">
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="lead in leads.data" :key="lead.id">
                            <td class="fw-semibold">{{ lead.name }}</td>
                            <td>{{ lead.email || '—' }}</td>
                            <td>{{ lead.phone || '—' }}</td>
                            <td>{{ lead.source || '—' }}</td>
                            <td><span class="badge" :class="badgeClass(lead.status)">{{ lead.status }}</span></td>
                            <td class="text-end">
                                <button
                                    v-if="lead.status !== 'converted'"
                                    @click="convert(lead.id)"
                                    class="btn btn-sm btn-outline-success me-1"
                                >Convert</button>
                                <Link :href="`/leads/${lead.id}/edit`" class="btn btn-sm btn-outline-secondary me-1">Edit</Link>
                                <button @click="destroy(lead.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="leads.data.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">No leads yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <Pagination :links="leads.links" />
        </div>
    </AuthenticatedLayout>
</template>
