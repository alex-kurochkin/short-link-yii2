<?php
/**
 * CodeUniquenessChecker interface
 */

namespace app\contracts;

interface CodeUniquenessChecker
{
    /**
     * Проверить, существует ли код в хранилище
     * 
     * @param string $code
     * @return bool
     */
    public function exists(string $code): bool;
}
