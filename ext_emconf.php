<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Form API',
    'description'      => 'Form API for extbase and fluid',
    'category'         => 'misc',
    'author'           => 'Ameos team',
    'author_email'     => 'typo3dev@ameos.com',
    'author_company'   => 'Ameos',
    'shy'              => '',
    'priority'         => '',
    'module'           => '',
    'state'            => 'stable',
    'internal'         => '',
    'uploadfolder'     => '0',
    'createDirs'       => '',
    'modify_tables'    => '',
    'clearCacheOnLoad' => 0,
    'lockType'         => '',
    'version'          => '1.3.9',
    'autoload'         => ['psr-4' => ['Ameos\\AmeosForm\\' => 'Classes']],
    'constraints'      => [
        'depends' => [
            'typo3' => '6.2.0-8.7.99',
            'php'   => '5.4.1-7.0.99'
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
    
];
