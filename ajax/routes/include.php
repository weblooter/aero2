<?php
/**
 * Допускается указание handler'а в формате '<class_name>:<method_name>'
 */
return [
    /*
    'cart' => [
        'path' => '/cart',
        'handler' => \Local\Core\Ajax\Handler\CartHandler::class . ':list',
    ],
    'cart_update' => [
        'path' => '/cart/{basket_id}',
        'methods' => ['PUT'],
        'handler' => \Local\Core\Ajax\Handler\CartHandler::class . ':update',
        'args' => [
            'basket_id' => '[0-9]+'
        ],
    ],
    'transport_modification' => [
        'path' => '/reference/transport/{man_id}/{model_id}/{body_id}/{year_from}/{year_to}/modification',
        'handler' => \Local\Core\Ajax\Handler\Reference\TransportHandler::class . ':modification',
        'args' => [
            'man_id' => '[0-9]+',
            'model_id' => '[0-9]+',
            'body_id' => '[0-9]+',
            'year_from' => '[0-9]+',
            'year_to' => '[0-9]{0,}',
        ]
    ],
    */

    'company_delete' => [
        'path' => '/company/delete/{company_id}/',
        'methods' => ['POST'],
        'args' => ['company_id' => '[0-9]+'],
        'handler' => \Local\Core\Ajax\Handler\Company::class.':delete'
    ],

    'store_delete' => [
        'path' => '/store/delete/{store_id}/',
        'methods' => ['POST'],
        'args' => ['store_id' => '[0-9]+'],
        'handler' => \Local\Core\Ajax\Handler\Store::class.':delete'
    ],
    'store_change_tariff' => [
        'path' => '/store/change_tariff/{store_id}/{tariff_code}/',
        'methods' => ['POST'],
        'args' => ['store_id' => '[0-9]+', 'tariff_code' => '[A-Z0-9\_]+'],
        'handler' => \Local\Core\Ajax\Handler\Store::class.':changeTariff'
    ],

    'tradingplatform_delete' => [
        'path' => '/trading-platform/delete/{tp_id}/',
        'methods' => ['POST'],
        'args' => ['tp_id' => '[0-9]+'],
        'handler' => \Local\Core\Ajax\Handler\TradingPlatform::class.':delete'
    ],

    'tradingplatform_form_refresh_row' => [
        'path' => '/trading-platform-form/refresh-row/',
        'methods' => ['POST'],
        'handler' => \Local\Core\Ajax\Handler\TradingPlatform::class.':refreshRow'
    ],

    'system_auth_authorize' => [
        'path' => '/system-auth-authorize/',
        'methods' => ['POST'],
        'handler' => \Local\Core\Ajax\Handler\SystemAuthAuthorize::class.':try'
    ],

    'system_auth_register' => [
        'path' => '/system-auth-register/',
        'methods' => ['POST'],
        'handler' => \Local\Core\Ajax\Handler\SystemAuthRegister::class.':try'
    ]
];
