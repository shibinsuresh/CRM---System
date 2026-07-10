<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    activity: Object,
    types: Array,
    contacts: Array,
    deals: Array,
});

const form = useForm({
    type: props.activity.type,
    subject: props.activity.subject,
    notes: props.activity.notes,
    due_date: props.activity.due_date ? String(props.activity.due_date).substring(0, 10) : '',
    contact_id: props.activity.contact_id ?? '',
    deal_id: props.activity.deal_id ?? '',
    completed: props.activity.completed,
});

const submit = () => form.put(`/activities/${props.activity.id}`);
</script>

<template>
    <Head title="Edit Activity" />

    <AuthenticatedLayout>
        <h1 class="h3 mb-3">Edit Activity</h1>

        <div class="card shadow-sm border-0" style="max-width: 640px;">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select v-model="form.type" class="form-select text-capitalize" :class="{ 'is-invalid': form.errors.type }">
                                <option v-for="t in types" :key="t" :value="t">{{ t }}</option>
                            </select>
                            <div class="invalid-feedback">{{ form.errors.type }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due date</label>
                            <input v-model="form.due_date" type="date" class="form-control" :class="{ 'is-invalid': form.errors.due_date }" />
                            <div class="invalid-feedback">{{ form.errors.due_date }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <input v-model="form.subject" type="text" class="form-control" :class="{ 'is-invalid': form.errors.subject }" />
                        <div class="invalid-feedback">{{ form.errors.subject }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea v-model="form.notes" rows="3" class="form-control" :class="{ 'is-invalid': form.errors.notes }"></textarea>
                        <div class="invalid-feedback">{{ form.errors.notes }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Related contact</label>
                            <select v-model="form.contact_id" class="form-select" :class="{ 'is-invalid': form.errors.contact_id }">
                                <option value="">— None —</option>
                                <option v-for="c in contacts" :key="c.id" :value="c.id">{{ c.first_name }} {{ c.last_name }}</option>
                            </select>
                            <div class="invalid-feedback">{{ form.errors.contact_id }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Related deal</label>
                            <select v-model="form.deal_id" class="form-select" :class="{ 'is-invalid': form.errors.deal_id }">
                                <option value="">— None —</option>
                                <option v-for="d in deals" :key="d.id" :value="d.id">{{ d.title }}</option>
                            </select>
                            <div class="invalid-feedback">{{ form.errors.deal_id }}</div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input id="completed" v-model="form.completed" type="checkbox" class="form-check-input" />
                        <label for="completed" class="form-check-label">Completed</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">Update</button>
                        <Link href="/activities" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
