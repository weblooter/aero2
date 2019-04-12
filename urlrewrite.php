<?
$arUrlRewrite = array(
    array(
        "CONDITION" => "#^/rest/#",
        "RULE" => "",
        "ID" => "",
        "PATH" => "/bitrix/services/rest/index.php",
        "SORT" => "100",
    ),

    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/edit/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/company-edit.php",
        "SORT" => "110",
    ),
    array(
        "CONDITION" => "#^/personal/company/add/(\?.*)?$#",
        "RULE" => "TMP=$1",
        "ID" => "",
        "PATH" => "/personal/company-add.php",
        "SORT" => "120",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/company-detail.php",
        "SORT" => "130",
    ),
    array(
        "CONDITION" => "#^/personal/company/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/company-list.php",
        "SORT" => "140",
    ),

    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/store/([0-9]+)/edit/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&STORE_ID=$2&TMP=$3",
        "ID" => "",
        "PATH" => "/personal/store-edit.php",
        "SORT" => "210",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/store/add/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/store-add.php",
        "SORT" => "220",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/store/([0-9]+)/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&STORE_ID=$2&TMP=$3",
        "ID" => "",
        "PATH" => "/personal/store-detail.php",
        "SORT" => "230",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/store/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/store-list.php",
        "SORT" => "240",
    ),

    array(
        "CONDITION" => "#^/personal/balance/(\?.*)?$#",
        "RULE" => "",
        "ID" => "",
        "PATH" => "/personal/balance.php",
        "SORT" => "310",
    ),
    array(
        "CONDITION" => "#^/personal/balance/top-up/(\?.*)?$#",
        "RULE" => "",
        "ID" => "",
        "PATH" => "/personal/balance-top-up.php",
        "SORT" => "320",
    ),

    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/store/([0-9]+)/tradingplatform/add/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&STORE_ID=$2&TMP=$3",
        "ID" => "",
        "PATH" => "/personal/tradingplatform-add.php",
        "SORT" => "410",
    ),
);
?>