<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/inertia-vue3';

const user = computed(() => usePage().props.value.auth.user);
</script>

<template>
    <Head title="Welcome" />

    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 text-center px-3">
        <h1 class="display-4 fw-bold text-primary mb-3">CRM System</h1>
        <p class="lead text-muted mb-4">
            Manage your customers and records with Laravel + Vue 3 + Inertia.
        </p>

        <div class="d-flex gap-2">
            <template v-if="user">
                <Link href="/dashboard" class="btn btn-primary btn-lg">Go to Dashboard</Link>
            </template>
            <template v-else>
                <Link href="/login" class="btn btn-primary btn-lg">Log in</Link>
                <Link href="/register" class="btn btn-outline-primary btn-lg">Register</Link>
            </template>
        </div>
    </div>
</template>
