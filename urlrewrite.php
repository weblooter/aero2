<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => '100',
  ),
  16 => 
  array (
    'CONDITION' => '#^/blog/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/blog/index.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/edit/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&TMP=$2',
    'ID' => '',
    'PATH' => '/personal/company-edit.php',
    'SORT' => '110',
  ),
  2 => 
  array (
    'CONDITION' => '#^/personal/company/add/(\\?.*)?$#',
    'RULE' => 'TMP=$1',
    'ID' => '',
    'PATH' => '/personal/company-add.php',
    'SORT' => '120',
  ),
  3 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&TMP=$2',
    'ID' => '',
    'PATH' => '/personal/company-detail.php',
    'SORT' => '130',
  ),
  4 => 
  array (
    'CONDITION' => '#^/personal/company/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&TMP=$2',
    'ID' => '',
    'PATH' => '/personal/company-list.php',
    'SORT' => '140',
  ),
  5 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/store/([0-9]+)/edit/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&STORE_ID=$2&TMP=$3',
    'ID' => '',
    'PATH' => '/personal/store-edit.php',
    'SORT' => '210',
  ),
  6 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/store/add/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&TMP=$2',
    'ID' => '',
    'PATH' => '/personal/store-add.php',
    'SORT' => '220',
  ),
  7 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/store/([0-9]+)/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&STORE_ID=$2&TMP=$3',
    'ID' => '',
    'PATH' => '/personal/store-detail.php',
    'SORT' => '230',
  ),
  8 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/store/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&TMP=$2',
    'ID' => '',
    'PATH' => '/personal/store-list.php',
    'SORT' => '240',
  ),
  9 => 
  array (
    'CONDITION' => '#^/personal/balance/(\\?.*)?$#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/personal/balance.php',
    'SORT' => '310',
  ),
  10 => 
  array (
    'CONDITION' => '#^/personal/balance/top-up/(\\?.*)?$#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/personal/balance-top-up.php',
    'SORT' => '320',
  ),
  11 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/store/([0-9]+)/tradingplatform/add/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&STORE_ID=$2&TMP=$3',
    'ID' => '',
    'PATH' => '/personal/tradingplatform-add.php',
    'SORT' => '410',
  ),
  12 => 
  array (
    'CONDITION' => '#^/personal/company/([0-9]+)/store/([0-9]+)/tradingplatform/([0-9]+)/(\\?.*)?$#',
    'RULE' => 'COMPANY_ID=$1&STORE_ID=$2&TP_ID=$3&TMP=$4',
    'ID' => '',
    'PATH' => '/personal/tradingplatform-edit.php',
    'SORT' => '430',
  ),
  13 => 
  array (
    'CONDITION' => '#^/personal/help/support/([0-9]+)/(\\?.*)?$#',
    'RULE' => 'SUPPORT_ID=$1&TMP=$2',
    'ID' => '',
    'PATH' => '/personal/help/support/detail.php',
    'SORT' => '510',
  ),
  15 => 
  array (
    'CONDITION' => '#^/personal/help/support/admin/([0-9]+)/(\\?.*)?$#',
    'RULE' => 'SUPPORT_ID=$1&TMP=$2',
    'ID' => '',
    'PATH' => '/personal/help/support/detail-admin.php',
    'SORT' => '520',
  ),
  14 => 
  array (
    'CONDITION' => '#^/personal/help/support/admin/(\\?.*)?$#',
    'RULE' => 'TMP=$1',
    'ID' => '',
    'PATH' => '/personal/help/support/detail-admin.php',
    'SORT' => '520',
  ),
);
