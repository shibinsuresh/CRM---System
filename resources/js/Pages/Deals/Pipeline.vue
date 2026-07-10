<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import draggable from 'vuedraggable';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    board: Array,
    stages: Array,
});

// Local, mutable copy so vuedraggable can move cards between columns.
const columns = ref(props.board.map((col) => ({ ...col, deals: [...col.deals] })));

const money = (v) => '$' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 0 });

const columnTotal = (deals) => deals.reduce((sum, d) => sum + Number(d.value), 0);

const stageClass = (stage) => ({
    new: 'text-secondary',
    qualified: 'text-info',
    proposal: 'text-primary',
    won: 'text-success',
    lost: 'text-danger',
}[stage] || 'text-secondary');

// When a card is dropped into a column, persist its new stage.
const onChange = (stage, event) => {
    if (event.added) {
        const deal = event.added.element;
        Inertia.patch(`/deals/${deal.id}/stage`, { stage }, {
            preserveScroll: true,
            preserveState: true,
        });
    }
};
</script>

<template>
    <Head title="Pipeline" />

    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Sales Pipeline</h1>
            <Link href="/deals" class="btn btn-outline-secondary">List view</Link>
        </div>

        <p class="text-muted">Drag a deal card between columns to change its stage.</p>

        <div class="d-flex gap-3 overflow-auto pb-3">
            <div v-for="col in columns" :key="col.stage" class="flex-shrink-0" style="width: 280px;">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-uppercase small" :class="stageClass(col.stage)">{{ col.stage }}</span>
                            <span class="badge bg-light text-dark">{{ col.deals.length }}</span>
                        </div>
                        <div class="small text-muted">{{ money(columnTotal(col.deals)) }}</div>
                    </div>
                    <div class="card-body bg-light" style="min-height: 400px;">
                        <draggable
                            :list="col.deals"
                            group="deals"
                            item-key="id"
                            :animation="150"
                            ghost-class="opacity-50"
                            @change="(e) => onChange(col.stage, e)"
                        >
                            <template #item="{ element }">
                                <div class="card mb-2 shadow-sm deal-card" style="cursor: grab;">
                                    <div class="card-body p-2">
                                        <div class="fw-semibold small">{{ element.title }}</div>
                                        <div class="text-success small">{{ money(element.value) }}</div>
                                        <div class="text-muted small" v-if="element.company">{{ element.company.name }}</div>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
