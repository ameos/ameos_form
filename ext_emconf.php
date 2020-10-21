<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Form API',
    'description'      => 'Form API for extbase and fluid',
    'category'         => 'misc',
    'author'           => 'Ameos team',
    'author_email'     => 'typo3dev@ameos.com',
    'author_company'   => 'Ameos',
    'state'            => 'stable',
    'version'          => '1.4.7-dev',
    'constraints'      => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'php'   => '7.2.0-7.4.99'
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
    
];
