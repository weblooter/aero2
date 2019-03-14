<?php

$aMenu = [];

$arModelData = [];

if( class_exists(\Local\Core\Inner\AdminHelper\Data\Company\AdminList::class) )
{
    $lDataList = ( new \Local\Core\Inner\AdminHelper\Data\Company\AdminList() )->getAdminUri();
    if( $lDataList->isSuccess() )
    {
        $lDataEdit = ( new \Local\Core\Inner\AdminHelper\Data\Company\AdminEdit() )->getAdminUri();

        $arModelData[] = [
            "text"     => "Компании",
            'url'      => $lDataList->getData()['uri'],
            "more_url" => ($lDataEdit->isSuccess()) ? [$lDataEdit->getData()["uri"]] : [],
            "icon"     => ""
        ];
    }

}

if( class_exists(\Local\Core\Inner\AdminHelper\Data\Site\AdminList::class) )
{
    $lDataList = ( new \Local\Core\Inner\AdminHelper\Data\Site\AdminList() )->getAdminUri();
    if( $lDataList->isSuccess() )
    {
        $lDataEdit = ( new \Local\Core\Inner\AdminHelper\Data\Site\AdminEdit() )->getAdminUri();

        $arModelData[] = [
            "text"     => "Сайты",
            'url'      => $lDataList->getData()['uri'],
            "more_url" => ($lDataEdit->isSuccess()) ? [$lDataEdit->getData()["uri"]] : [],
            "icon"     => ""
        ];
    }

}

/* *********** */
/* СПРАВОЧНИКИ */
/* *********** */
$arModelReferences = [];

if( class_exists(\Local\Core\Inner\AdminHelper\Reference\Measure\AdminList::class) )
{
    $lDataList = ( new \Local\Core\Inner\AdminHelper\Reference\Measure\AdminList() )->getAdminUri();
    if( $lDataList->isSuccess() )
    {
        $lDataEdit = ( new \Local\Core\Inner\AdminHelper\Reference\Measure\AdminEdit() )->getAdminUri();

        $arModelReferences[] = [
            "text"     => "Единицы измерения",
            'url'      => $lDataList->getData()['uri'],
            "more_url" => ($lDataEdit->isSuccess()) ? [$lDataEdit->getData()["uri"]] : [],
            "icon"     => "",
        ];
    }

}

/* ******************** */
/* СПРАВОЧНИКИ FEED API */
/* ******************** */

$arFeedApiReferences = [];


/*
 * Для примера
$aMenu = [
    [
        "parent_menu" => "global_menu_services", // поместим в раздел "Сервис"
        "sort" => 100,                    // вес пункта меню
        "url" => "admin_helper_route.php?adminEntity=10",  // ссылка на пункте меню
        "text" => "10",       // текст пункта меню
        "title" => "222", // текст всплывающей подсказки
        "icon" => "form_menu_icon", // малая иконка
        "page_icon" => "form_page_icon", // большая иконка
        "items_id" => "menu_webforms",  // идентификатор ветви
        "items" => [
            array(
                "text" => "111",
                "url" => "admin_helper_route.php?adminEntity=111",
                "icon" => "form_menu_icon",
                "page_icon" => "form_page_icon",
                "more_url" => array(),
                "title" => GetMessage("FORM_RESULTS_ALT")
            ),
            array(
                "text" => "3333",
                "url" => "admin_helper_route.php?adminEntity=3333&lang=" . LANGUAGE_ID . "&WEB_FORM_ID=" . $zr["ID"],
                "icon" => "form_menu_icon",
                "page_icon" => "form_page_icon",
                "more_url" => array(),
                "title" => GetMessage("FORM_RESULTS_ALT")
            )
        ],
    ],
*/

if( !empty($arModelData) )
{
    $aMenu[] = [
        "parent_menu" => "global_menu_local_core",
        "text"        => "Model\Data",
        'url'         => '',
        "items_id"    => "model_data",
        "icon"        => "iblock_menu_icon_types",
        "sort"        => 1,
        'items'       => $arModelData
    ];
}

if( !empty($arModelReferences) )
{
    $aMenu[] = [
        "parent_menu" => "global_menu_local_core",
        "text"        => "Справочники",
        'url'         => '',
        "items_id"    => "model_reference",
        "icon"        => "highloadblock_menu_icon",
        "sort"        => 99,
        'items'       => $arModelReferences
    ];
}

if( !empty($arFeedApiReferences) )
{
    $aMenu[] = [
        "parent_menu" => "global_menu_local_core",
        "text"        => "Справочники FeedApi",
        'url'         => '',
        "items_id"    => "model_reference_feed_api",
        "icon"        => "highloadblock_menu_icon",
        "sort"        => 99,
        'items'       => $arFeedApiReferences
    ];
}

return ( !empty($aMenu) ) ? $aMenu : false;

