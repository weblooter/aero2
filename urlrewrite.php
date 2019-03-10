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
        "CONDITION" => "#^/personal/company/add/([^?]{0,})#",
        "RULE" => "TMP=$1",
        "ID" => "",
        "PATH" => "/personal/company/add.php",
        "SORT" => "100",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/edit/([^?]{0,})#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/company/edit.php",
        "SORT" => "100",
    ),
    array(
        "CONDITION" => "#^/personal/company/([0-9]+)/([^?]{0,})#",
        "RULE" => "COMPANY_ID=$1&TMP=$2",
        "ID" => "",
        "PATH" => "/personal/company/detail.php",
        "SORT" => "100",
    ),
);
?>