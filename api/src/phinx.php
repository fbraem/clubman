<?php
/**
 * Phinx migration configuration
 */
$application = \Core\Clubman::getApplication();
$config = $application->getConfig();

$environments = $config->database->toArray();
$environments['default_database'] = $config->default_database;

return [
    'paths' => [
        'migrations' => [
            __DIR__ . '/domain/User/migrations',
            __DIR__ . '/domain/News/migrations'
        ]
    ],
    'environments' => $environments
];