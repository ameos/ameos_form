<?php

declare(strict_types=1);

$EM_CONF['ameos_form'] = [
    'title' => 'Form API',
    'description' => 'Form API for extbase and fluid',
    'category' => 'misc',
    'author' => 'Ameos team',
    'author_email' => 'typo3dev@ameos.com',
    'author_company' => 'Ameos',
    'state' => 'stable',
    'version' => '4.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '14.3.0-14.3.99',
            'php' => '8.3.0-8.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
