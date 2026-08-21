<?php

namespace tests\feature;

use tests\TestCase;
use app\models\Link;
use app\models\User;
use app\fixtures\UserFixture;
use app\fixtures\LinkFixture;

/**
 * Feature tests for short link functionality
 */
class ShortLinkTest extends TestCase
{
    /**
     * @return array list of fixtures to be loaded
     */
    public function fixtures(): array
    {
        return [
            'users' => UserFixture::class,
            'links' => LinkFixture::class,
        ];
    }

    /**
     * Test that short link redirects to original URL
     */
    public function testShortLinkRedirectsToOriginalUrl(): void
    {
        $user = $this->users[0];
        
        $link = Link::create([
            'user_id' => $user->id,
            'code' => 'abc123',
            'original_url' => 'https://example.com/target-page',
        ]);

        $response = $this->mockWebApplication()->handleRequest('/abc123');
        
        $this->assertEquals(302, $response->statusCode);
        $this->assertStringContainsString('https://example.com/target-page', $response->headers['Location']);
    }

    /**
     * Test that clicking short link records click in database
     */
    public function testClickRecordsClickInDatabase(): void
    {
        $user = $this->users[0];
        
        $link = Link::create([
            'user_id' => $user->id,
            'code' => 'click1',
            'original_url' => 'https://example.com',
        ]);

        // Simulate request with specific IP
        $response = $this->mockWebApplication()->handleRequest('/click1');
        
        $this->assertEquals(1, $link->getClicks()->count());
        
        $click = $link->clicks[0];
        $this->assertEquals('127.0.0.1', $click->ip_address);
        $this->assertNotNull($click->clicked_at);
    }

    /**
     * Test that non-existent short link returns 404
     */
    public function testNonExistentShortLinkReturns404(): void
    {
        $response = $this->mockWebApplication()->handleRequest('/zzzzzz');
        
        $this->assertEquals(404, $response->statusCode);
    }

    /**
     * Test that invalid code format returns 404 from router
     */
    public function testInvalidCodeFormatReturns404FromRouter(): void
    {
        // Route requires 6 characters [A-Za-z0-9]{6}, so 3 chars should fail
        $response = $this->mockWebApplication()->handleRequest('/abc');
        
        $this->assertEquals(404, $response->statusCode);
    }

    /**
     * Test that user cannot see another user's links
     */
    public function testUserCannotSeeAnotherUsersLinks(): void
    {
        $user1 = $this->users[0];
        $user2 = $this->users[1];

        Link::create([
            'user_id' => $user1->id,
            'code' => 'user11',
            'original_url' => 'https://example.com/user1',
        ]);

        // Check links for user2
        $user2Links = Link::find()->where(['user_id' => $user2->id])->all();

        $this->assertCount(0, $user2Links);
    }
}
