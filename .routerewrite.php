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
                        "Добавление компании",
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
    'site'    => [
        'list'   => [
            'URL'         => '/personal/company/#COMPANY_ID#/site/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'company',
                        'detail',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );
                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Список сайтов",
                        \Local\Core\Inner\Route::getRouteTo(
                            'site',
                            'list',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
                        )
                    );
                }
        ],
        'detail' => [
            'URL' => '/personal/company/#COMPANY_ID#/site/#SITE_ID#/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'site',
                        'list',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );

                    $GLOBALS['APPLICATION']->AddChainItem(
                        \Local\Core\Inner\Site\Base::getSiteDomain($arParams['SITE_ID']),
                        \Local\Core\Inner\Route::getRouteTo(
                            'site',
                            'detail',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#SITE_ID#' => $arParams['SITE_ID']]
                        )
                    );
                }
        ],
        'add'    => [
            'URL' => '/personal/company/#COMPANY_ID#/site/add/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'site',
                        'list',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );

                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Добавление сайта",
                        \Local\Core\Inner\Route::getRouteTo(
                            'site',
                            'add',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
                        )
                    );
                }
        ],
        'edit'   => [
            'URL' => '/personal/company/#COMPANY_ID#/site/#SITE_ID#/edit/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'site',
                        'list',
                        ['COMPANY_ID' => $arParams['COMPANY_ID']]
                    );

                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Редактирование ".\Local\Core\Inner\Site\Base::getSiteDomain($arParams['SITE_ID']),
                        \Local\Core\Inner\Route::getRouteTo(
                            'site',
                            'edit',
                            [
                                '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                                '#SITE_ID#'    => $arParams['SITE_ID']
                            ]
                        )
                    );
                }
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