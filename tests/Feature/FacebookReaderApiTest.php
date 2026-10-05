<?php

namespace Tests\Feature;

use App\Models\FacebookPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacebookReaderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pages_can_be_created_updated_and_deleted(): void
    {
        $created = $this->postJson('/api/pages', [
            'name' => 'Example Page',
            'username' => 'example-page',
            'category' => 'News',
            'enabled' => true,
            'sort_order' => 20,
        ]);

        $created->assertCreated()
            ->assertJsonPath('data.name', 'Example Page')
            ->assertJsonPath('data.enabled', true);

        $pageId = $created->json('data.id');

        $this->putJson("/api/pages/{$pageId}", [
            'name' => 'Updated Example Page',
            'enabled' => false,
            'sort_order' => 2,
        ])->assertOk()
            ->assertJsonPath('data.name', 'Updated Example Page')
            ->assertJsonPath('data.enabled', false);

        $this->deleteJson("/api/pages/{$pageId}")->assertNoContent();
        $this->assertDatabaseMissing('facebook_pages', ['id' => $pageId]);
    }

    public function test_feed_only_returns_enabled_pages_with_posts_in_newest_first_order(): void
    {
        FacebookPage::query()->where('username', 'jpj')->update(['enabled' => false]);

        $response = $this->getJson('/api/feed?per_page=4');

        $response->assertOk()
            ->assertJsonMissing(['username' => 'jpj'])
            ->assertJsonPath('data.0.username', 'pasti-malaysia')
            ->assertJsonCount(2, 'data.0.posts');

        $posts = $response->json('data.0.posts');
        $this->assertGreaterThanOrEqual(
            strtotime($posts[1]['published_at']),
            strtotime($posts[0]['published_at']),
        );
    }

    public function test_page_posts_are_paginated_and_include_normalized_media(): void
    {
        $page = FacebookPage::query()->where('username', 'jakim')->firstOrFail();

        $this->getJson("/api/pages/{$page->id}/posts?per_page=1")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('data.0.media.0.media_type', 'image');
    }
}
