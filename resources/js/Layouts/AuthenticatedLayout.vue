<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/inertia-vue3';

const user = computed(() => usePage().props.value.auth.user);
const flashSuccess = computed(() => usePage().props.value.flash?.success);
</script>

<template>
    <div class="min-vh-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container">
                <Link class="navbar-brand fw-bold" href="/dashboard">CRM System</Link>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNav"
                    aria-controls="mainNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <Link class="nav-link" href="/dashboard">Dashboard</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/leads">Leads</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/contacts">Contacts</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/companies">Companies</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/deals">Deals</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/pipeline">Pipeline</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="nav-link" href="/activities">Activities</Link>
                        </li>
                        <li class="nav-item" v-if="user?.role === 'admin'">
                            <Link class="nav-link" href="/users">Users</Link>
                        </li>
                    </ul>

                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                {{ user?.name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><Link class="dropdown-item" href="/profile">Profile</Link></li>
                                <li><hr class="dropdown-divider" /></li>
                                <li>
                                    <Link class="dropdown-item" href="/logout" method="post" as="button">
                                        Log Out
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <div v-if="flashSuccess" class="alert alert-success" role="alert">
                    {{ flashSuccess }}
                </div>
                <slot />
            </div>
        </main>
    </div>
</template>
