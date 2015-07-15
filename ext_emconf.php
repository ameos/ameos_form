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
	'state'            => 'beta',
	'internal'         => '',
	'uploadfolder'     => '0',
	'createDirs'       => 'typo3temp/ameos_form/tempupload/',
	'modify_tables'    => '',
	'clearCacheOnLoad' => 0,
	'lockType'         => '',
	'version'          => '1.1.0-dev',
	'constraints'      => [
		'depends' => [
			'typo3' => '6.2.0-7.99.99',
			'php'   => '5.4.1-5.6.99'
		],
		'conflicts' => [],
		'suggests'  => [],
	],
];
