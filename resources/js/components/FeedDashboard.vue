<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { api } from '../api';
import type { Page, Post } from '../types';
import PostCard from './PostCard.vue';

const pages = ref<Page[]>([]);
const loading = ref(true);
const error = ref('');
const loadingMore = ref<Record<number, boolean>>({});
const nextPages = ref<Record<number, number>>({});
const hasMore = ref<Record<number, boolean>>({});

async function loadFeed(): Promise<void> {
    loading.value = true;
    error.value = '';

    try {
        pages.value = await api.feed();
        nextPages.value = Object.fromEntries(pages.value.map((page) => [page.id, 2]));
        hasMore.value = Object.fromEntries(pages.value.map((page) => [page.id, (page.posts?.length ?? 0) === 4]));
    } catch (requestError) {
        error.value = requestError instanceof Error ? requestError.message : 'Unable to load the feed.';
    } finally {
        loading.value = false;
    }
}

async function loadMore(page: Page): Promise<void> {
    loadingMore.value[page.id] = true;

    try {
        const result = await api.posts(page.id, nextPages.value[page.id] ?? 2);
        const existing = page.posts ?? [];
        page.posts = [...existing, ...result.data];
        nextPages.value[page.id] = result.meta.current_page + 1;
        hasMore.value[page.id] = result.meta.current_page < result.meta.last_page;
    } catch (requestError) {
        error.value = requestError instanceof Error ? requestError.message : 'Unable to load more posts.';
    } finally {
        loadingMore.value[page.id] = false;
    }
}

function postKey(post: Post): number {
    return post.id;
}

onMounted(loadFeed);
</script>

<template>
    <section aria-labelledby="feed-heading">
        <div class="section-heading">
            <div>
                <p class="eyebrow">SELECTED PAGES</p>
                <h2 id="feed-heading">Latest updates</h2>
            </div>
            <button class="button button-secondary" :disabled="loading" @click="loadFeed">
                {{ loading ? 'Refreshing...' : 'Refresh' }}
            </button>
        </div>

        <p class="source-note">Development mock data is displayed locally. No Facebook or Meta API requests are made.</p>
        <p v-if="error" class="error-message" role="alert">{{ error }}</p>
        <p v-if="loading" class="empty-state">Loading selected Pages...</p>
        <p v-else-if="pages.length === 0" class="empty-state">Enable a Page in Pages to see its column here.</p>

        <div v-else class="feed-grid">
            <article v-for="page in pages" :key="page.id" class="feed-column">
                <header class="column-header">
                    <div>
                        <h3>{{ page.name }}</h3>
                        <span>{{ page.category || 'Other' }}</span>
                    </div>
                    <span class="status-dot" aria-label="Enabled"></span>
                </header>

                <div v-if="(page.posts?.length ?? 0) === 0" class="column-empty">No mock posts yet.</div>
                <PostCard v-for="post in page.posts" :key="postKey(post)" :post="post" />

                <button
                    v-if="hasMore[page.id]"
                    class="load-more"
                    :disabled="loadingMore[page.id]"
                    @click="loadMore(page)"
                >
                    {{ loadingMore[page.id] ? 'Loading...' : 'Load more' }}
                </button>
            </article>
        </div>
    </section>
</template>
