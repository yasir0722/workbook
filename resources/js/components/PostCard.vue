<script setup lang="ts">
import type { Post } from '../types';

defineProps<{
    post: Post;
}>();

function formattedDate(value: string | null): string {
    if (!value) {
        return 'Date unavailable';
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}
</script>

<template>
    <article class="post-card">
        <time :datetime="post.published_at || undefined">{{ formattedDate(post.published_at) }}</time>
        <p v-if="post.message" class="post-message">{{ post.message }}</p>
        <p v-else class="post-message post-message-muted">This post has no text.</p>

        <div v-if="post.media.length" :class="['media-grid', { multiple: post.media.length > 1 }]">
            <img
                v-for="media in post.media"
                :key="media.id"
                :src="media.thumbnail_url || media.media_url"
                :alt="post.message ? 'Post media' : 'Post image'"
                loading="lazy"
            >
        </div>

        <a v-if="post.permalink" class="open-link" :href="post.permalink" target="_blank" rel="noopener noreferrer">
            Open Facebook <span aria-hidden="true">↗</span>
        </a>
        <span v-else class="open-link unavailable">Original link unavailable</span>
    </article>
</template>
