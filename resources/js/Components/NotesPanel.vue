<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    notableType: String, // 'contact' | 'company' | 'deal'
    notableId: Number,
    notes: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    notable_type: props.notableType,
    notable_id: props.notableId,
    body: '',
});

const add = () => {
    form.post('/notes', {
        preserveScroll: true,
        onSuccess: () => form.reset('body'),
    });
};

const remove = (id) => {
    if (confirm('Delete this note?')) {
        Inertia.delete(`/notes/${id}`, { preserveScroll: true });
    }
};

const formatDate = (d) => new Date(d).toLocaleString();
</script>

<template>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="h6 text-muted text-uppercase mb-3">Notes</h2>

            <form @submit.prevent="add" class="mb-3">
                <textarea
                    v-model="form.body"
                    rows="2"
                    class="form-control mb-2"
                    :class="{ 'is-invalid': form.errors.body }"
                    placeholder="Add a note…"
                ></textarea>
                <div class="invalid-feedback">{{ form.errors.body }}</div>
                <button type="submit" class="btn btn-sm btn-primary" :disabled="form.processing">Add note</button>
            </form>

            <ul class="list-unstyled mb-0">
                <li v-for="note in notes" :key="note.id" class="border-top py-2">
                    <div class="d-flex justify-content-between">
                        <div class="small text-muted">
                            {{ note.user ? note.user.name : 'Unknown' }} · {{ formatDate(note.created_at) }}
                        </div>
                        <button @click="remove(note.id)" class="btn btn-sm btn-link text-danger p-0">Delete</button>
                    </div>
                    <div style="white-space: pre-wrap;">{{ note.body }}</div>
                </li>
                <li v-if="notes.length === 0" class="text-muted py-2">No notes yet.</li>
            </ul>
        </div>
    </div>
</template>
