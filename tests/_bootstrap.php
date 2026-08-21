<?php
/**
 * Bootstrap file for tests
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Load test configuration
$config = require __DIR__ . '/../config/web.php';

// Create application instance for testing
(new yii\web\Application($config));
