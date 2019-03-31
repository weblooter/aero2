<?
$arLocalRoutes = [
    'company' => [
        'list'   => [
            'URL'         => '/personal/company/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Мои компании",
                        \Local\Core\Inner\Route::getRouteTo(
                            'company',
                            'list'
                        )
                    );
                }
        ],
        'add'    => [
            'URL'         => '/personal/company/add/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'company',
                        'list'
                    );
                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Создание компании",
                        \Local\Core\Inner\Route::getRouteTo(
                            'company',
                            'add'
                        )
                    );
                }
        ],
        'detail' => [
            'URL'         => '/personal/company/#COMPANY_ID#/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'company',
                        'list'
                    );
                    $GLOBALS['APPLICATION']->AddChainItem(
                        \Local\Core\Inner\Company\Base::getCompanyName($arParams['COMPANY_ID']),
                        \Local\Core\Inner\Route::getRouteTo(
                            'company',
                            'detail',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
                        )
                    );
                }
        ],
        'edit'   => [
            'URL'         => '/personal/company/#COMPANY_ID#/edit/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'company',
                        'list'
                    );
                    $GLOBALS['APPLICATION']->AddChainItem('Редактирование компании '.\Local\Core\Inner\Company\Base::getCompanyName($arParams['COMPANY_ID']));
                }
        ],
    ],
    'store'    => [
        'list'   => [
            'URL'         => '/personal/company/#COMPANY_ID#/store/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'company',
                        'detail',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );
                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Список магазинов",
                        \Local\Core\Inner\Route::getRouteTo(
                            'store',
                            'list',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
                        )
                    );
                }
        ],
        'detail' => [
            'URL'         => '/personal/company/#COMPANY_ID#/store/#STORE_ID#/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'store',
                        'list',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );

                    $GLOBALS['APPLICATION']->AddChainItem(
                        \Local\Core\Inner\Store\Base::getStoreName($arParams['STORE_ID']),
                        \Local\Core\Inner\Route::getRouteTo(
                            'store',
                            'detail',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']]
                        )
                    );
                }
        ],
        'add'    => [
            'URL'         => '/personal/company/#COMPANY_ID#/store/add/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'store',
                        'list',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );

                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Добавление сайта",
                        \Local\Core\Inner\Route::getRouteTo(
                            'store',
                            'add',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
                        )
                    );
                }
        ],
        'edit'   => [
            'URL'         => '/personal/company/#COMPANY_ID#/store/#STORE_ID#/edit/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'store',
                        'list',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );

                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Редактирование ".\Local\Core\Inner\Store\Base::getStoreName($arParams['STORE_ID']),
                        \Local\Core\Inner\Route::getRouteTo(
                            'store',
                            'edit',
                            [
                                '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                                '#STORE_ID#'    => $arParams['STORE_ID']
                            ]
                        )
                    );
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
        'convert' => [
            'URL' => '/personal/convert/'
        ],
    ],
    'bill'    => [
        'list'   => [
            'URL' => '/personal/company/#COMPANY_ID#/bill/'
        ],
        'detail' => [
            'URL' => '/personal/company/#COMPANY_ID#/bill/#BILL_ACCOUNT_ID#/'
        ],
    ]
];