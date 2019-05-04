<?
$arLocalRoutes = [
    'company' => [
        'list' => [
            'URL' => '/personal/company/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    $GLOBALS['APPLICATION']->AddChainItem("Компании", \Local\Core\Inner\Route::getRouteTo('company', 'list'));
                }
        ],
        'add' => [
            'URL' => '/personal/company/add/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'list');
                    $GLOBALS['APPLICATION']->AddChainItem("Добавить компанию", \Local\Core\Inner\Route::getRouteTo('company', 'add'));
                }
        ],
        'detail' => [
            'URL' => '/personal/company/#COMPANY_ID#/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'list');
                    $GLOBALS['APPLICATION']->AddChainItem(\Local\Core\Inner\Company\Base::getCompanyName($arParams['COMPANY_ID']),
                        \Local\Core\Inner\Route::getRouteTo('company', 'detail', ['#COMPANY_ID#' => $arParams['COMPANY_ID']]));
                }
        ],
        'edit' => [
            'URL' => '/personal/company/#COMPANY_ID#/edit/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'list');
                    $GLOBALS['APPLICATION']->AddChainItem('Редактирование компании '.\Local\Core\Inner\Company\Base::getCompanyName($arParams['COMPANY_ID']));
                }
        ],
    ],
    'store' => [
        'list' => [
            'URL' => '/personal/company/#COMPANY_ID#/store/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'detail', ['COMPANY_ID' => $arParams['COMPANY_ID']]);
                    $GLOBALS['APPLICATION']->AddChainItem("Магазины", \Local\Core\Inner\Route::getRouteTo('store', 'list', ['#COMPANY_ID#' => $arParams['COMPANY_ID']]));
                }
        ],
        'detail' => [
            'URL' => '/personal/company/#COMPANY_ID#/store/#STORE_ID#/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('store', 'list', ['COMPANY_ID' => $arParams['COMPANY_ID']]);

                    $GLOBALS['APPLICATION']->AddChainItem(\Local\Core\Inner\Store\Base::getStoreName($arParams['STORE_ID']),
                        \Local\Core\Inner\Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']]));
                }
        ],
        'add' => [
            'URL' => '/personal/company/#COMPANY_ID#/store/add/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('store', 'list', ['COMPANY_ID' => $arParams['COMPANY_ID']]);

                    $GLOBALS['APPLICATION']->AddChainItem("Добавление сайта", \Local\Core\Inner\Route::getRouteTo('store', 'add', ['#COMPANY_ID#' => $arParams['COMPANY_ID']]));
                }
        ],
        'edit' => [
            'URL' => '/personal/company/#COMPANY_ID#/store/#STORE_ID#/edit/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('store', 'list', ['COMPANY_ID' => $arParams['COMPANY_ID']]);

                    $GLOBALS['APPLICATION']->AddChainItem("Редактирование ".\Local\Core\Inner\Store\Base::getStoreName($arParams['STORE_ID']), \Local\Core\Inner\Route::getRouteTo('store', 'edit', [
                        '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                        '#STORE_ID#' => $arParams['STORE_ID']
                    ]));
                }
        ],
    ],
    'development' => [
        'robofeed' => [
            'URL' => '/development/robofeed-v1/'
        ],
        'references' => [
            'URL' => '/development/references/'
        ],
    ],
    'tools' => [
        'list' => [
            'URL' => '/personal/tools/'
        ],
        'converter' => [
            'URL' => '/personal/tools/converter/'
        ],
    ],

    'balance' => [
        'list' => [
            'URL' => '/personal/balance/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    $GLOBALS['APPLICATION']->AddChainItem("Баланс", \Local\Core\Inner\Route::getRouteTo('balance', 'list'));
                }
        ],
        'top-up' => [
            'URL' => '/personal/balance/top-up/?handler=#HANDLER#',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('balance', 'list');
                    $GLOBALS['APPLICATION']->AddChainItem("Пополнить баланс", \Local\Core\Inner\Route::getRouteTo('balance', 'top-up', ['#HANDLER#' => '']));
                }
        ]
    ],
    'settings' => [
        'list' => [
            'URL' => '/personal/settings/',
            'BREADCRUMBS' => function ($arParams = [])
            {
                $GLOBALS['APPLICATION']->AddChainItem("Настройки", \Local\Core\Inner\Route::getRouteTo('settings', 'list'));
            }
        ],
    ],
    'tradingplatform' => [
        'list' => [
            'URL' => '/personal/company/#COMPANY_ID#/store/#STORE_ID#/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('store', 'detail', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]);
                }
        ],
        'add' => [
            'URL' => '/personal/company/#COMPANY_ID#/store/#STORE_ID#/tradingplatform/add/?handler=#HANDLER#',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('tradingplatform', 'list', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]);

                    $GLOBALS['APPLICATION']->AddChainItem('Добавить торговую площадку',
                        \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']]));
                }
        ],
        'edit' => [
            'URL' => '/personal/company/#COMPANY_ID#/store/#STORE_ID#/tradingplatform/#TP_ID#/',
            'BREADCRUMBS' => function ($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs('tradingplatform', 'list', ['COMPANY_ID' => $arParams['COMPANY_ID'], 'STORE_ID' => $arParams['STORE_ID']]);

                    $GLOBALS['APPLICATION']->AddChainItem('Редактирование "'.\Local\Core\Inner\TradingPlatform\Base::getName($arParams['TP_ID']).'"',
                        \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'edit', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arParams['TP_ID']]));
                }
        ]
    ]
];