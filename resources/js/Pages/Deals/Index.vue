<script setup>
import { ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import Pagination from '../../Components/Pagination.vue';

const props = defineProps({
    deals: Object,
    filters: Object,
    stages: Array,
});

const search = ref(props.filters.search || '');
const stage = ref(props.filters.stage || '');

const applyFilters = debounce(() => {
    Inertia.get('/deals', { search: search.value, stage: stage.value }, { preserveState: true, replace: true });
}, 300);

watch([search, stage], applyFilters);

const badgeClass = (stage) => ({
    new: 'bg-secondary',
    qualified: 'bg-info',
    proposal: 'bg-primary',
    won: 'bg-success',
    lost: 'bg-danger',
}[stage] || 'bg-secondary');

const money = (v) => '$' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2 });

const destroy = (id) => {
    if (confirm('Delete this deal?')) {
        Inertia.delete(`/deals/${id}`);
    }
};
</script>

<template>
    <Head title="Deals" />

    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Deals</h1>
            <div class="d-flex gap-2">
                <Link href="/pipeline" class="btn btn-outline-primary">Pipeline board</Link>
                <Link href="/deals/create" class="btn btn-primary">+ Add Deal</Link>
            </div>
        </div>

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <input v-model="search" type="search" class="form-control" style="max-width: 320px;" placeholder="Search deals…" />
            <select v-model="stage" class="form-select text-capitalize" style="max-width: 200px;">
                <option value="">All stages</option>
                <option v-for="s in stages" :key="s" :value="s">{{ s }}</option>
            </select>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Value</th>
                            <th>Stage</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th>Close date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="deal in deals.data" :key="deal.id">
                            <td class="fw-semibold">{{ deal.title }}</td>
                            <td>{{ money(deal.value) }}</td>
                            <td><span class="badge" :class="badgeClass(deal.stage)">{{ deal.stage }}</span></td>
                            <td>{{ deal.contact ? (deal.contact.first_name + ' ' + (deal.contact.last_name || '')) : '—' }}</td>
                            <td>{{ deal.company ? deal.company.name : '—' }}</td>
                            <td>{{ deal.expected_close_date || '—' }}</td>
                            <td class="text-end">
                                <Link :href="`/deals/${deal.id}/edit`" class="btn btn-sm btn-outline-secondary me-1">Edit</Link>
                                <button @click="destroy(deal.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="deals.data.length === 0">
                            <td colspan="7" class="text-center text-muted py-4">No deals yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <Pagination :links="deals.links" />
        </div>
    </AuthenticatedLayout>
</template>
