<?php

namespace tests\feature;

use tests\TestCase;
use app\models\Link;
use app\models\User;
use app\services\CodeGenerator;
use app\fixtures\UserFixture;
use app\fixtures\LinkFixture;
use Yii;

/**
 * Integration tests for CodeGenerator with real database
 */
class CodeGeneratorIntegrationTest extends TestCase
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
     * Test that generator creates unique code in real database
     */
    public function testGeneratorCreatesUniqueCodeInRealDatabase(): void
    {
        $user = $this->users[0];

        // Create a link with known code
        Link::create([
            'user_id' => $user->id,
            'code' => 'aaaaaa',
            'original_url' => 'https://example.com',
        ]);

        // Create generator with real checker
        $checker = new app\services\CodeCheckers\EloquentCodeUniquenessChecker();
        $generator = new CodeGenerator($checker);
        
        $code = $generator->generate();

        // Code should not match existing one
        $this->assertNotEquals('aaaaaa', $code);

        // Code should be unique (not exist in database)
        $existingLink = Link::findOne(['code' => $code]);
        $this->assertNull($existingLink);
    }

    /**
     * Test that generator handles multiple concurrent generations
     */
    public function testGeneratorHandlesMultipleConcurrentGenerations(): void
    {
        $user = $this->users[0];

        // Create several links with known codes
        $knownCodes = ['bbbbbb', 'cccccc', 'dddddd'];
        foreach ($knownCodes as $code) {
            Link::create([
                'user_id' => $user->id,
                'code' => $code,
                'original_url' => 'https://example.com/' . $code,
            ]);
        }

        // Create generator with real checker
        $checker = new app\services\CodeCheckers\EloquentCodeUniquenessChecker();
        $generator = new CodeGenerator($checker);
        
        // Generate multiple codes
        $generatedCodes = [];
        for ($i = 0; $i < 5; $i++) {
            $generatedCodes[] = $generator->generate();
        }

        // All generated codes should be unique
        $uniqueCodes = array_unique($generatedCodes);
        $this->assertCount(5, $uniqueCodes);

        // None should match known codes
        foreach ($generatedCodes as $code) {
            $this->assertNotContains($code, $knownCodes);
        }
    }
}
