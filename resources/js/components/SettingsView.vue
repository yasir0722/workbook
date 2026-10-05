<template>
    <section class="settings-view" aria-labelledby="settings-heading">
        <p class="eyebrow">APPLICATION SETTINGS</p>
        <h2 id="settings-heading">Local development</h2>
        <div class="settings-card">
            <h3>Content source</h3>
            <p>
                The application currently uses development mock data through
                <code>MockFacebookContentSource</code>. No Facebook Login, Meta token, Graph API request, or scraping is enabled.
            </p>
        </div>
        <div class="settings-card">
            <h3>Future integration</h3>
            <p>
                A future source can replace the mock provider only after PPCA approval and the official proof of concept documented in
                <code>docs/facebook-api-feasibility.md</code>.
            </p>
        </div>
    </section>
</template>
