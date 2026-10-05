<script setup lang="ts">
import { ref } from 'vue';
import FeedDashboard from './FeedDashboard.vue';
import PagesManager from './PagesManager.vue';
import SettingsView from './SettingsView.vue';

type View = 'feed' | 'pages' | 'settings';

const currentView = ref<View>('feed');
</script>

<template>
    <main class="app-shell">
        <header class="app-header">
            <div>
                <p class="eyebrow">DEVELOPMENT MODE</p>
                <h1>Facebook Reader</h1>
                <p class="subtitle">A quiet dashboard for intentionally selected Pages.</p>
            </div>
            <span class="mock-badge">Mock data only</span>
        </header>

        <nav class="main-nav" aria-label="Primary navigation">
            <button :class="{ active: currentView === 'feed' }" @click="currentView = 'feed'">Feed</button>
            <button :class="{ active: currentView === 'pages' }" @click="currentView = 'pages'">Pages</button>
            <button :class="{ active: currentView === 'settings' }" @click="currentView = 'settings'">Settings</button>
        </nav>

        <FeedDashboard v-if="currentView === 'feed'" />
        <PagesManager v-else-if="currentView === 'pages'" />
        <SettingsView v-else />
    </main>
</template>
