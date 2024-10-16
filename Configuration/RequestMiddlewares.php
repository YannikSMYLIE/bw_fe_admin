<?php

use BoergenerWebdesign\BwFeAdmin\Middleware\SwitchUserMiddleware;

return [
    'frontend' => [
        'feadmin-switch' => [
            'target' => SwitchUserMiddleware::class,
            'after' => [
                'typo3/cms-frontend/tsfe',
            ],
            'before' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ]
        ],
    ],
];