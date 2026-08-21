<?php

namespace tests\feature;

use tests\TestCase;
use app\models\Link;
use app\models\User;
use app\fixtures\UserFixture;
use app\fixtures\LinkFixture;

/**
 * Feature tests for Link model relationships and accessors
 */
class LinkModelTest extends TestCase
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
     * Test that a link belongs to a user
     */
    public function testLinkBelongsToUser(): void
    {
        $user = $this->users[0];
        $link = $this->links[0];

        $this->assertInstanceOf(User::class, $link->user);
        $this->assertEquals($user->id, $link->user->id);
    }

    /**
     * Test that a link has many clicks
     */
    public function testLinkHasManyClicks(): void
    {
        $link = $this->links[0];

        \app\models\Click::create([
            'link_id' => $link->id,
            'ip_address' => '127.0.0.1',
            'clicked_at' => time(),
        ]);
        \app\models\Click::create([
            'link_id' => $link->id,
            'ip_address' => '192.168.1.1',
            'clicked_at' => time(),
        ]);

        $this->assertCount(2, $link->clicks);
    }

    /**
     * Test short_url accessor returns correct format
     */
    public function testShortUrlAccessorReturnsCorrectFormat(): void
    {
        $link = new Link([
            'code' => 'test12',
            'original_url' => 'https://example.com',
            'user_id' => $this->users[0]->id,
        ]);

        // In Yii2 url() returns baseUrl from config
        $shortUrl = $link->short_url;
        $this->assertStringContainsString('test12', $shortUrl);
    }

    /**
     * Test clicks_count accessor returns number of clicks
     */
    public function testClicksCountAccessorReturnsNumberOfClicks(): void
    {
        $link = $this->links[0];

        \app\models\Click::create([
            'link_id' => $link->id,
            'ip_address' => '127.0.0.1',
            'clicked_at' => time(),
        ]);

        $clicksCount = $link->clicks_count;
        $this->assertEquals(1, $clicksCount);
    }
}
