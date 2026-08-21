<?php
/**
 * EloquentCodeUniquenessChecker class
 * 
 * Checks if a short link code already exists in the database.
 */

namespace app\services\CodeCheckers;

use app\models\Link;
use app\contracts\CodeUniquenessChecker;

class EloquentCodeUniquenessChecker implements CodeUniquenessChecker
{
    /**
     * {@inheritdoc}
     */
    public function exists(string $code): bool
    {
        return Link::find()->where(['code' => $code])->exists();
    }
}
