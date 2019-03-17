<?php


$aMenu = [];

$arModelData = [];


/* ********** */
/* Model\Data */
/* ********** */
\CLocalCore::addItemToMenu(
    $arModelData,
    \Local\Core\Inner\AdminHelper\Data\Company\AdminList::class,
    \Local\Core\Inner\AdminHelper\Data\Company\AdminEdit::class,
    'Компании'
);
\CLocalCore::addItemToMenu(
    $arModelData,
    \Local\Core\Inner\AdminHelper\Data\Site\AdminList::class,
    \Local\Core\Inner\AdminHelper\Data\Site\AdminEdit::class,
    'Сайты'
);


/* *********** */
/* СПРАВОЧНИКИ */
/* *********** */
$arModelReferences = [];

\CLocalCore::addItemToMenu(
    $arModelReferences,
    \Local\Core\Inner\AdminHelper\Reference\Measure\AdminList::class,
    \Local\Core\Inner\AdminHelper\Reference\Measure\AdminEdit::class,
    'Единицы измерения'
);
\CLocalCore::addItemToMenu(
    $arModelReferences,
    \Local\Core\Inner\AdminHelper\Reference\Currency\AdminList::class,
    \Local\Core\Inner\AdminHelper\Reference\Currency\AdminEdit::class,
    'Валюты'
);
\CLocalCore::addItemToMenu(
    $arModelReferences,
    \Local\Core\Inner\AdminHelper\Reference\Country\AdminList::class,
    \Local\Core\Inner\AdminHelper\Reference\Country\AdminEdit::class,
    'Страны'
);


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
        "text" => "Model\Data",
        'url' => '',
        "items_id" => "model_data",
        "icon" => "iblock_menu_icon_types",
        "sort" => 1,
        'items' => $arModelData
    ];
}

if( !empty($arModelReferences) )
{
    $aMenu[] = [
        "parent_menu" => "global_menu_local_core",
        "text" => "Справочники",
        'url' => '',
        "items_id" => "model_reference",
        "icon" => "highloadblock_menu_icon",
        "sort" => 99,
        'items' => $arModelReferences
    ];
}

return ( !empty($aMenu) ) ? $aMenu : false;

