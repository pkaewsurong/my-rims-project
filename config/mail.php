<?php
// config/mail.php
// SMTP Configuration for sending emails
//
// Credentials are loaded from environment variables.
// For local development, create config/mail.local.php (gitignored) to override.

// Load local overrides if they exist (for development)
$localConfig = __DIR__ . '/mail.local.php';
if (file_exists($localConfig)) {
    return require $localConfig;
}

return [
    // SMTP Server Settings
    'host'       => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port'       => getenv('MAIL_PORT') ?: 587,
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',

    // Authentication (set via environment variables)
    'username'   => getenv('MAIL_USERNAME') ?: '',
    'password'   => getenv('MAIL_PASSWORD') ?: '',

    // Sender Info
    'from_email' => getenv('MAIL_FROM_ADDRESS') ?: '',
    'from_name'  => getenv('MAIL_FROM_NAME') ?: 'RIMS - Research & Innovation Management System',
];
