<script setup>
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import GuestLayout from '../../Layouts/GuestLayout.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <GuestLayout>
        <h2 class="h4 fw-bold text-center mb-4">Log in</h2>

        <div v-if="status" class="alert alert-success">{{ status }}</div>

        <form @submit.prevent="submit">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.email }"
                    required
                    autofocus
                />
                <div class="invalid-feedback">{{ form.errors.email }}</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    required
                />
                <div class="invalid-feedback">{{ form.errors.password }}</div>
            </div>

            <div class="form-check mb-3">
                <input id="remember" v-model="form.remember" type="checkbox" class="form-check-input" />
                <label for="remember" class="form-check-label">Remember me</label>
            </div>

            <button
                type="submit"
                class="btn btn-primary w-100"
                :disabled="form.processing"
            >
                Log in
            </button>

            <div class="text-center mt-3">
                <Link href="/register" class="text-decoration-none">Need an account? Register</Link>
            </div>
        </form>
    </GuestLayout>
</template>
