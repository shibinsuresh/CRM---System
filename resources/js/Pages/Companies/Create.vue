<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';

const form = useForm({
    name: '',
    industry: '',
    website: '',
    phone: '',
    address: '',
});

const submit = () => form.post('/companies');
</script>

<template>
    <Head title="Add Company" />

    <AuthenticatedLayout>
        <h1 class="h3 mb-3">Add Company</h1>

        <div class="card shadow-sm border-0" style="max-width: 640px;">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }" />
                        <div class="invalid-feedback">{{ form.errors.name }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Industry</label>
                        <input v-model="form.industry" type="text" class="form-control" :class="{ 'is-invalid': form.errors.industry }" />
                        <div class="invalid-feedback">{{ form.errors.industry }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <input v-model="form.website" type="text" class="form-control" :class="{ 'is-invalid': form.errors.website }" />
                        <div class="invalid-feedback">{{ form.errors.website }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" type="text" class="form-control" :class="{ 'is-invalid': form.errors.phone }" />
                        <div class="invalid-feedback">{{ form.errors.phone }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input v-model="form.address" type="text" class="form-control" :class="{ 'is-invalid': form.errors.address }" />
                        <div class="invalid-feedback">{{ form.errors.address }}</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">Save</button>
                        <Link href="/companies" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
