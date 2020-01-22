<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../themes/',
            'adminTemplates' => __DIR__ . '/../adminSrc/',
        ],

        'db' => [
	        'driver' => 'mysql',
	        'host' => '',
	        'database' => '',
	        'username' => '',
	        'password' => '',
	        'prefix'    => '',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
