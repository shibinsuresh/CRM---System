<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import NotesPanel from '../../Components/NotesPanel.vue';

const props = defineProps({
    deal: Object,
    contacts: Array,
    companies: Array,
    stages: Array,
    notes: Array,
});

const form = useForm({
    title: props.deal.title,
    value: props.deal.value,
    stage: props.deal.stage,
    expected_close_date: props.deal.expected_close_date ? String(props.deal.expected_close_date).substring(0, 10) : '',
    contact_id: props.deal.contact_id ?? '',
    company_id: props.deal.company_id ?? '',
});

const submit = () => form.put(`/deals/${props.deal.id}`);
</script>

<template>
    <Head title="Edit Deal" />

    <AuthenticatedLayout>
        <h1 class="h3 mb-3">Edit Deal</h1>

        <div class="card shadow-sm border-0" style="max-width: 640px;">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': form.errors.title }" />
                        <div class="invalid-feedback">{{ form.errors.title }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Value ($) *</label>
                            <input v-model="form.value" type="number" step="0.01" min="0" class="form-control" :class="{ 'is-invalid': form.errors.value }" />
                            <div class="invalid-feedback">{{ form.errors.value }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stage</label>
                            <select v-model="form.stage" class="form-select" :class="{ 'is-invalid': form.errors.stage }">
                                <option v-for="s in stages" :key="s" :value="s">{{ s }}</option>
                            </select>
                            <div class="invalid-feedback">{{ form.errors.stage }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expected close date</label>
                        <input v-model="form.expected_close_date" type="date" class="form-control" :class="{ 'is-invalid': form.errors.expected_close_date }" />
                        <div class="invalid-feedback">{{ form.errors.expected_close_date }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact</label>
                            <select v-model="form.contact_id" class="form-select" :class="{ 'is-invalid': form.errors.contact_id }">
                                <option value="">— None —</option>
                                <option v-for="c in contacts" :key="c.id" :value="c.id">{{ c.first_name }} {{ c.last_name }}</option>
                            </select>
                            <div class="invalid-feedback">{{ form.errors.contact_id }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company</label>
                            <select v-model="form.company_id" class="form-select" :class="{ 'is-invalid': form.errors.company_id }">
                                <option value="">— None —</option>
                                <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <div class="invalid-feedback">{{ form.errors.company_id }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">Update</button>
                        <Link href="/deals" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-3" style="max-width: 640px;">
            <NotesPanel notable-type="deal" :notable-id="deal.id" :notes="notes" />
        </div>
    </AuthenticatedLayout>
</template>
