<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { api } from '../api';
import type { Page, PagePayload } from '../types';
import PageForm from './PageForm.vue';

const pages = ref<Page[]>([]);
const selectedPage = ref<Page | null>(null);
const loading = ref(true);
const submitting = ref(false);
const error = ref('');

async function loadPages(): Promise<void> {
    loading.value = true;
    error.value = '';

    try {
        pages.value = await api.pages();
    } catch (requestError) {
        error.value = requestError instanceof Error ? requestError.message : 'Unable to load Pages.';
    } finally {
        loading.value = false;
    }
}

async function savePage(payload: PagePayload): Promise<void> {
    submitting.value = true;
    error.value = '';

    try {
        if (selectedPage.value) {
            await api.updatePage(selectedPage.value.id, payload);
        } else {
            await api.createPage(payload);
        }

        selectedPage.value = null;
        await loadPages();
    } catch (requestError) {
        error.value = requestError instanceof Error ? requestError.message : 'Unable to save the Page.';
    } finally {
        submitting.value = false;
    }
}

async function togglePage(page: Page): Promise<void> {
    await savePage({
        name: page.name,
        username: page.username,
        url: page.url,
        category: page.category,
        enabled: !page.enabled,
        sort_order: page.sort_order,
    });
}

async function deletePage(page: Page): Promise<void> {
    if (!window.confirm(`Remove ${page.name} and its local posts?`)) {
        return;
    }

    try {
        await api.deletePage(page.id);
        if (selectedPage.value?.id === page.id) {
            selectedPage.value = null;
        }
        await loadPages();
    } catch (requestError) {
        error.value = requestError instanceof Error ? requestError.message : 'Unable to remove the Page.';
    }
}

function editPage(page: Page): void {
    selectedPage.value = page;
}

function addPage(): void {
    selectedPage.value = null;
}

onMounted(loadPages);
</script>

<template>
    <section class="pages-layout" aria-labelledby="pages-heading">
        <div>
            <div class="section-heading">
                <div>
                    <p class="eyebrow">LOCAL SELECTION</p>
                    <h2 id="pages-heading">My Pages</h2>
                </div>
                <button class="button" @click="addPage">+ Add Page</button>
            </div>
            <p class="source-note">Pages are managed locally. This does not import or access a Facebook following list.</p>
            <p v-if="error" class="error-message" role="alert">{{ error }}</p>
            <p v-if="loading" class="empty-state">Loading Pages...</p>

            <div v-else class="page-list">
                <article v-for="page in pages" :key="page.id" class="page-row">
                    <label class="page-toggle">
                        <input :checked="page.enabled" type="checkbox" @change="togglePage(page)">
                        <span>
                            <strong>{{ page.name }}</strong>
                            <small>{{ page.username ? `@${page.username}` : 'No local username' }}</small>
                        </span>
                    </label>
                    <span class="category-pill">{{ page.category || 'Other' }}</span>
                    <span class="sort-order">#{{ page.sort_order }}</span>
                    <div class="row-actions">
                        <button class="text-button" @click="editPage(page)">Edit</button>
                        <button class="text-button danger" @click="deletePage(page)">Remove</button>
                    </div>
                </article>
            </div>
        </div>

        <aside class="form-panel">
            <PageForm :page="selectedPage" :submitting="submitting" @save="savePage" @cancel="selectedPage = null" />
        </aside>
    </section>
</template>
