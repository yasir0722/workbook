<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_the_vue_shell(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('id="app"', false);
    }
}
