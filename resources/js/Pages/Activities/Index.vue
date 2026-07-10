<script setup>
import { ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import debounce from 'lodash/debounce';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import Pagination from '../../Components/Pagination.vue';

const props = defineProps({
    activities: Object,
    filters: Object,
    types: Array,
});

const search = ref(props.filters.search || '');
const type = ref(props.filters.type || '');

const applyFilters = debounce(() => {
    Inertia.get('/activities', { search: search.value, type: type.value }, { preserveState: true, replace: true });
}, 300);

watch([search, type], applyFilters);

const typeIcon = (type) => ({
    call: '📞',
    meeting: '👥',
    email: '✉️',
    task: '✅',
}[type] || '•');

const toggle = (id) => {
    Inertia.patch(`/activities/${id}/toggle`, {}, { preserveScroll: true });
};

const destroy = (id) => {
    if (confirm('Delete this activity?')) {
        Inertia.delete(`/activities/${id}`);
    }
};

const relatedTo = (a) => {
    if (a.contact) return a.contact.first_name + ' ' + (a.contact.last_name || '');
    if (a.deal) return a.deal.title;
    return '—';
};
</script>

<template>
    <Head title="Activities" />

    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Activities &amp; Tasks</h1>
            <Link href="/activities/create" class="btn btn-primary">+ Add Activity</Link>
        </div>

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <input v-model="search" type="search" class="form-control" style="max-width: 320px;" placeholder="Search activities…" />
            <select v-model="type" class="form-select text-capitalize" style="max-width: 200px;">
                <option value="">All types</option>
                <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
            </select>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Related to</th>
                            <th>Due</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="a in activities.data" :key="a.id" :class="{ 'text-muted': a.completed }">
                            <td>
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    :checked="a.completed"
                                    @change="toggle(a.id)"
                                />
                            </td>
                            <td :class="{ 'text-decoration-line-through': a.completed }">{{ a.subject }}</td>
                            <td>{{ typeIcon(a.type) }} <span class="text-capitalize">{{ a.type }}</span></td>
                            <td>{{ relatedTo(a) }}</td>
                            <td>{{ a.due_date ? String(a.due_date).substring(0, 10) : '—' }}</td>
                            <td class="text-end">
                                <Link :href="`/activities/${a.id}/edit`" class="btn btn-sm btn-outline-secondary me-1">Edit</Link>
                                <button @click="destroy(a.id)" class="btn btn-sm btn-outline-danger">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="activities.data.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">No activities yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <Pagination :links="activities.links" />
        </div>
    </AuthenticatedLayout>
</template>
