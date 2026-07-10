<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    stats: Object,
    byStage: Array,
    recentDeals: Array,
    recentLeads: Array,
    upcomingTasks: Array,
});

const money = (v) => '$' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 0 });

const maxStageValue = computed(() => Math.max(1, ...props.byStage.map((s) => s.value)));

const stageColor = (stage) => ({
    new: '#6c757d',
    qualified: '#0dcaf0',
    proposal: '#0d6efd',
    won: '#198754',
    lost: '#dc3545',
}[stage] || '#6c757d');

const cards = computed(() => [
    { label: 'Contacts', value: props.stats.contacts, href: '/contacts', color: 'primary' },
    { label: 'Companies', value: props.stats.companies, href: '/companies', color: 'info' },
    { label: 'Open Leads', value: props.stats.open_leads, href: '/leads', color: 'warning' },
    { label: 'Open Deals', value: props.stats.open_deals, href: '/deals', color: 'secondary' },
    { label: 'Open Tasks', value: props.stats.open_tasks, href: '/activities', color: 'danger' },
    { label: 'Pipeline Value', value: money(props.stats.pipeline_value), href: '/pipeline', color: 'dark' },
    { label: 'Won Value', value: money(props.stats.won_value), href: '/deals', color: 'success' },
]);

const relatedTo = (a) => {
    if (a.contact) return a.contact.first_name + ' ' + (a.contact.last_name || '');
    if (a.deal) return a.deal.title;
    return '';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <h1 class="h3 mb-4">Dashboard</h1>

        <!-- Metric cards -->
        <div class="row g-3 mb-4">
            <div v-for="card in cards" :key="card.label" class="col-6 col-md-4 col-xl-2">
                <Link :href="card.href" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">{{ card.label }}</div>
                            <div class="h4 mb-0 mt-1" :class="`text-${card.color}`">{{ card.value }}</div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>

        <div class="row g-3">
            <!-- Deals by stage chart -->
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h2 class="h6 text-muted text-uppercase mb-3">Deals by stage</h2>
                        <div v-for="s in byStage" :key="s.stage" class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-capitalize fw-semibold">{{ s.stage }} <span class="text-muted">({{ s.count }})</span></span>
                                <span class="text-muted">{{ money(s.value) }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div
                                    class="progress-bar"
                                    role="progressbar"
                                    :style="{ width: (s.value / maxStageValue * 100) + '%', backgroundColor: stageColor(s.stage) }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent activity -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h2 class="h6 text-muted text-uppercase mb-3">Recent deals</h2>
                        <ul class="list-unstyled mb-0">
                            <li v-for="d in recentDeals" :key="d.id" class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ d.title }}</span>
                                <span class="text-success">{{ money(d.value) }}</span>
                            </li>
                            <li v-if="recentDeals.length === 0" class="text-muted py-2">No deals yet.</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <h2 class="h6 text-muted text-uppercase mb-3">Recent leads</h2>
                        <ul class="list-unstyled mb-0">
                            <li v-for="l in recentLeads" :key="l.id" class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ l.name }}</span>
                                <span class="badge bg-light text-dark text-capitalize">{{ l.status }}</span>
                            </li>
                            <li v-if="recentLeads.length === 0" class="text-muted py-2">No leads yet.</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h6 text-muted text-uppercase mb-3">Upcoming tasks</h2>
                        <ul class="list-unstyled mb-0">
                            <li v-for="t in upcomingTasks" :key="t.id" class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ t.subject }} <small class="text-muted" v-if="relatedTo(t)">· {{ relatedTo(t) }}</small></span>
                                <span class="text-muted small">{{ t.due_date ? String(t.due_date).substring(0, 10) : 'no date' }}</span>
                            </li>
                            <li v-if="upcomingTasks.length === 0" class="text-muted py-2">No open tasks.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
