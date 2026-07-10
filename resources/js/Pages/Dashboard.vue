<script setup>
import { Head } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="h4 mb-3">Dashboard</h2>
                        <p class="text-muted mb-0">
                            You're logged in! This page is rendered with Vue 3 + Inertia
                            and styled with Bootstrap 5.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
