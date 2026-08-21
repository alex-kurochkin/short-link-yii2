<?php

namespace tests\unit;

use tests\TestCase;
use app\models\Link;
use app\models\User;
use app\models\Click;

/**
 * Test case for Link model
 */
class LinkModelTest extends TestCase
{
    /**
     * @var array list of fixtures to be loaded
     */
    public function fixtures(): array
    {
        return [
            'users' => \app\fixtures\UserFixture::class,
            'links' => \app\fixtures\LinkFixture::class,
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

        Click::create([
            'link_id' => $link->id,
            'ip_address' => '127.0.0.1',
            'clicked_at' => time(),
        ]);
        Click::create([
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

        // В Yii2 url() возвращает baseUrl из конфига
        $shortUrl = $link->short_url;
        $this->assertStringContainsString('test12', $shortUrl);
    }

    /**
     * Test clicks_count accessor returns number of clicks
     */
    public function testClicksCountAccessorReturnsNumberOfClicks(): void
    {
        $link = $this->links[0];

        Click::create([
            'link_id' => $link->id,
            'ip_address' => '127.0.0.1',
            'clicked_at' => time(),
        ]);

        $clicksCount = $link->clicks_count;
        $this->assertEquals(1, $clicksCount);
    }
}
