<script setup>
import { Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 py-5">
        <Link href="/" class="mb-4 text-decoration-none">
            <h1 class="h3 fw-bold text-primary mb-0">CRM System</h1>
        </Link>

        <div class="card shadow-sm border-0" style="width: 100%; max-width: 26rem;">
            <div class="card-body p-4">
                <slot />
            </div>
        </div>
    </div>
</template>
