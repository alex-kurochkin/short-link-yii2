<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

/**
 * Link fixture for testing
 */
class LinkFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Link';
    
    public $depends = [
        UserFixture::class,
    ];
}
