<?php

namespace tests;

use yii\unit\TestCase as BaseTestCase;
use yii\test\DbFixtureTrait;

/**
 * Base test case for all tests
 */
abstract class TestCase extends BaseTestCase
{
    use DbFixtureTrait;

    /**
     * @return array list of fixture aliases to be loaded
     */
    public function fixtures(): array
    {
        return [];
    }

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed
    }

    /**
     * Tear down test environment
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        // Additional teardown if needed
    }
}
