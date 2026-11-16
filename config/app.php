<?php
/**
 * config/app.php
 * Application Environment and Core Configuration.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

return [
    'name'        => getenv('APP_NAME') ?: 'PharmaLink Online Pharmacy',
    'env'         => getenv('APP_ENV') ?: 'production',
    'debug'       => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'url'         => getenv('APP_URL') ?: 'http://pharmalink.test',
    'timezone'    => 'UTC',
    'locale'      => 'en_US',
    'version'     => '2.0.0',
    'license'     => 'NABP/FDA Clinical Pharmacy Portal Compliant',
];
