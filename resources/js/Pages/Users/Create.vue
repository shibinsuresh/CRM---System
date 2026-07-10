<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';

defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    role: 'sales_rep',
    password: '',
    password_confirmation: '',
});

const submit = () => form.post('/users');
</script>

<template>
    <Head title="Add User" />

    <AuthenticatedLayout>
        <h1 class="h3 mb-3">Add User</h1>

        <div class="card shadow-sm border-0" style="max-width: 640px;">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }" />
                        <div class="invalid-feedback">{{ form.errors.name }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': form.errors.email }" />
                        <div class="invalid-feedback">{{ form.errors.email }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select v-model="form.role" class="form-select text-capitalize" :class="{ 'is-invalid': form.errors.role }">
                            <option v-for="r in roles" :key="r" :value="r">{{ r.replace('_', ' ') }}</option>
                        </select>
                        <div class="invalid-feedback">{{ form.errors.role }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input v-model="form.password" type="password" class="form-control" :class="{ 'is-invalid': form.errors.password }" />
                        <div class="invalid-feedback">{{ form.errors.password }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <input v-model="form.password_confirmation" type="password" class="form-control" />
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">Save</button>
                        <Link href="/users" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
