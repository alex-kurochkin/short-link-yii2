<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

/**
 * User fixture for testing
 */
class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
    
    public $depends = [];
}
