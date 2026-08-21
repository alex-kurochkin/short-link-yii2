<?php
/**
 * URL Routing rules
 * Matches Laravel routes structure
 */
return [
    // Home page
    '' => 'site/index',
    
    // Authentication routes
    'login' => 'auth/login',
    'POST login' => 'auth/login',
    'register' => 'auth/register',
    'POST register' => 'auth/register',
    'logout' => 'auth/logout',
    
    // Short link redirect (must be last, with pattern constraint)
    '<code:[A-Za-z0-9]{6}>' => 'short-link/redirect',
];
