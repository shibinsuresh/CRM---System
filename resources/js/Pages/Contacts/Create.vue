<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';

defineProps({
    companies: Array,
});

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    job_title: '',
    company_id: '',
});

const submit = () => form.post('/contacts');
</script>

<template>
    <Head title="Add Contact" />

    <AuthenticatedLayout>
        <h1 class="h3 mb-3">Add Contact</h1>

        <div class="card shadow-sm border-0" style="max-width: 640px;">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <input v-model="form.first_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.first_name }" />
                            <div class="invalid-feedback">{{ form.errors.first_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input v-model="form.last_name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.last_name }" />
                            <div class="invalid-feedback">{{ form.errors.last_name }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" />
                        <div class="invalid-feedback">{{ form.errors.email }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" type="text" class="form-control" :class="{ 'is-invalid': form.errors.phone }" />
                        <div class="invalid-feedback">{{ form.errors.phone }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Job Title</label>
                        <input v-model="form.job_title" type="text" class="form-control" :class="{ 'is-invalid': form.errors.job_title }" />
                        <div class="invalid-feedback">{{ form.errors.job_title }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <select v-model="form.company_id" class="form-select" :class="{ 'is-invalid': form.errors.company_id }">
                            <option value="">— None —</option>
                            <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                        <div class="invalid-feedback">{{ form.errors.company_id }}</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">Save</button>
                        <Link href="/contacts" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
