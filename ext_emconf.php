<?php

$EM_CONF['ameos_form'] = [
    'title'            => 'Form API',
    'description'      => 'Form API for extbase and fluid',
    'category'         => 'misc',
    'author'           => 'Ameos team',
    'author_email'     => 'typo3dev@ameos.com',
    'author_company'   => 'Ameos',
    'state'            => 'beta',
    'version'          => '3.0.0-dev',
    'constraints'      => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'php'   => '8.0.0-8.3.99'
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
];
