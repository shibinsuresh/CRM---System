<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';

const statuses = ['new', 'contacted', 'qualified', 'lost'];

const form = useForm({
    name: '',
    email: '',
    phone: '',
    source: '',
    status: 'new',
    notes: '',
});

const submit = () => form.post('/leads');
</script>

<template>
    <Head title="Add Lead" />

    <AuthenticatedLayout>
        <h1 class="h3 mb-3">Add Lead</h1>

        <div class="card shadow-sm border-0" style="max-width: 640px;">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }" />
                        <div class="invalid-feedback">{{ form.errors.name }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" />
                            <div class="invalid-feedback">{{ form.errors.email }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input v-model="form.phone" type="text" class="form-control" :class="{ 'is-invalid': form.errors.phone }" />
                            <div class="invalid-feedback">{{ form.errors.phone }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Source</label>
                            <input v-model="form.source" type="text" class="form-control" placeholder="Website, Referral…" :class="{ 'is-invalid': form.errors.source }" />
                            <div class="invalid-feedback">{{ form.errors.source }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select v-model="form.status" class="form-select" :class="{ 'is-invalid': form.errors.status }">
                                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                            </select>
                            <div class="invalid-feedback">{{ form.errors.status }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea v-model="form.notes" rows="3" class="form-control" :class="{ 'is-invalid': form.errors.notes }"></textarea>
                        <div class="invalid-feedback">{{ form.errors.notes }}</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">Save</button>
                        <Link href="/leads" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
