<?
$arLocalRoutes = [
    'company' => [
        'list' => '/personal/company/',
        'add' => '/personal/company/add/',
        'detail' => '/personal/company/#COMPANY_ID#/',
        'edit' => '/personal/company/#COMPANY_ID#/edit/',
        'delete' => '/personal/company/#COMPANY_ID#/delete/',
    ],
    'site' => [
        'list' => '/personal/company/#COMPANY_ID#/site/',
        'detail' => '/personal/company/#COMPANY_ID#/site/#SITE_ID#/',
        'add' => '/personal/company/#COMPANY_ID#/site/add/',
        'edit' => '/personal/company/#COMPANY_ID#/site/#SITE_ID#/edit/',
    ],
    'bill' => [
        'list' => '/personal/company/#COMPANY_ID#/bill/',
        'detail' => '/personal/company/#COMPANY_ID#/bill/#BILL_ACCOUNT_ID#/',
    ]
];