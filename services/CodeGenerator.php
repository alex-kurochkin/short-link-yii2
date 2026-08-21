<?php
/**
 * CodeGenerator service class
 * 
 * Generates unique random codes for short links.
 */

namespace app\services;

use Yii;
use app\contracts\CodeUniquenessChecker;

class CodeGenerator
{
    /**
     * @var CodeUniquenessChecker
     */
    private CodeUniquenessChecker $checker;

    /**
     * Constructor
     * 
     * @param CodeUniquenessChecker $checker
     */
    public function __construct(CodeUniquenessChecker $checker)
    {
        $this->checker = $checker;
    }

    /**
     * Generate a unique code
     * 
     * @return string
     * @throws \RuntimeException if unable to generate unique code after max attempts
     */
    public function generate(): string
    {
        $length = Yii::$app->params['shortLinks']['codeLength'] ?? 6;
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $code = $this->generateRandomString($length);
            $attempts++;
        } while ($this->checker->exists($code) && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            throw new \RuntimeException('Не удалось сгенерировать уникальный код');
        }

        return $code;
    }

    /**
     * Generate a random string of specified length
     * 
     * @param int $length
     * @return string
     */
    private function generateRandomString(int $length): string
    {
        return Yii::$app->security->generateRandomString($length);
    }
}
