<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Form API',
    'description'      => 'Form API for extbase and fluid',
    'category'         => 'misc',
    'author'           => 'Ameos team',
    'author_email'     => 'typo3dev@ameos.com',
    'author_company'   => 'Ameos',
    'state'            => 'stable',
    'version'          => '2.0.0',
    'constraints'      => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'php'   => '8.0.0-8.2.99'
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
    
];
