<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Frontend Admin',
    'description' => 'Frontend Login aus dem Backend.',
    'category' => 'backend',
    'author' => 'Yannik Börgener',
    'author_email' => 'kontakt@boergener.de',
    'state' => 'alpha',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.2.3',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.12-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
