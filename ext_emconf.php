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
    'version'          => '1.4',
    'autoload'         => ['psr-4' => ['Ameos\\AmeosForm\\' => 'Classes']],
    'constraints'      => [
        'depends' => [
            'typo3' => '7.6.0-9.5.99',
            'php'   => '7.0.0-7.2.99'
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
    
];
