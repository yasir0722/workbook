<script setup lang="ts">
import { computed, reactive, watch } from 'vue';
import type { Page, PagePayload } from '../types';

const props = defineProps<{
    page?: Page | null;
    submitting: boolean;
}>();

const emit = defineEmits<{
    save: [payload: PagePayload];
    cancel: [];
}>();

const categories = ['Education', 'Government', 'Religion', 'Community', 'News', 'Technology', 'Other'];

const form = reactive<PagePayload>({
    name: '',
    username: null,
    url: null,
    category: 'Other',
    enabled: true,
});

const title = computed(() => (props.page ? 'Edit Page' : 'Add Page'));

function resetForm(): void {
    form.name = props.page?.name ?? '';
    form.username = props.page?.username ?? null;
    form.url = props.page?.url ?? null;
    form.category = props.page?.category ?? 'Other';
    form.enabled = props.page?.enabled ?? true;
    form.sort_order = props.page?.sort_order;
}

watch(() => props.page, resetForm, { immediate: true });

function submit(): void {
    emit('save', {
        ...form,
        username: form.username || null,
        url: form.url || null,
        category: form.category || null,
    });
}
</script>

<template>
    <form class="page-form" @submit.prevent="submit">
        <div class="form-heading">
            <h3>{{ title }}</h3>
            <button v-if="page" type="button" class="text-button" @click="emit('cancel')">Cancel</button>
        </div>

        <label>
            Page name
            <input v-model.trim="form.name" required maxlength="255" placeholder="Example Page">
        </label>
        <label>
            Local username
            <input v-model.trim="form.username" maxlength="255" placeholder="example-page">
        </label>
        <label>
            Page URL
            <input v-model.trim="form.url" type="url" maxlength="2048" placeholder="https://example.com/page">
        </label>
        <label>
            Category
            <select v-model="form.category">
                <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
            </select>
        </label>
        <label>
            Display order
            <input v-model.number="form.sort_order" type="number" min="0">
        </label>
        <label class="checkbox-label">
            <input v-model="form.enabled" type="checkbox">
            Show this Page in the feed
        </label>

        <button class="button" :disabled="submitting">{{ submitting ? 'Saving...' : 'Save Page' }}</button>
    </form>
</template>
