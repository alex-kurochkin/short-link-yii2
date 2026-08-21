<?php
/**
 * CacheCodeUniquenessChecker class
 * 
 * Checks if a short link code exists in cache.
 * Note: To use this implementation, you need to listen for Link creation events
 * and write the code to cache, and clear it on deletion.
 * Since this is a test task, this is not implemented in the project.
 */

namespace app\services\CodeCheckers;

use Yii;
use app\contracts\CodeUniquenessChecker;

class CacheCodeUniquenessChecker implements CodeUniquenessChecker
{
    /**
     * {@inheritdoc}
     */
    public function exists(string $code): bool
    {
        return Yii::$app->cache->exists("short_link_code_{$code}");
    }
}
