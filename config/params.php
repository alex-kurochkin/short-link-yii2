<?php
/**
 * Application parameters
 *
 * Reads settings from environment variables.
 */
return [
    'adminEmail' => getenv('ADMIN_EMAIL') ?: 'admin@example.com',
    'shortLinks' => [
        'codeLength' => (int) (getenv('SHORT_LINK_CODE_LENGTH') ?: 6),
        'checker' => getenv('SHORT_LINKS_CHECKER') ?: \app\services\CodeCheckers\EloquentCodeUniquenessChecker::class,
    ],
];
