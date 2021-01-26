<?php
return [
    'frontend' => [
        'feadmin-switch' => [
            'target' => \BoergenerWebdesign\BwFeAdmin\Middleware\SwitchUserMiddleware::class,
            'after' => [
                'typo3/cms-frontend/tsfe',
            ],
            'before' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ]
        ],
    ],
];