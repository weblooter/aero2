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
        "CONDITION" => "#^/personal/company/([0-9]+)/site/([0-9])/edit/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&SITE_ID=$2&TMP=$3",
        "ID" => "",
        "PATH" => "/personal/site-edit.php",
        "SORT" => "210",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/site/add/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/site-add.php",
        "SORT" => "220",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/site/([0-9])/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&SITE_ID=$2&TMP=$3",
        "ID" => "",
        "PATH" => "/personal/site-detail.php",
        "SORT" => "230",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/site/(\?.*)?$#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/site-list.php",
        "SORT" => "240",
    ),
);
?>