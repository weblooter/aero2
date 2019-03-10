<?php

$aMenu = [];


//$arTransportReference = [];

//$lTransportBodyList = (new \Local\Core\Inner\AdminHelper\Reference\Transport\Body\AdminList)->getAdminUri();
//if ($lTransportBodyList->isSuccess()) {
//    $lTransportBodyEdit = (new \Local\Core\Inner\AdminHelper\Reference\Transport\Body\AdminEdit())->getAdminUri();
//    $arTransportReference[] = [
//        "text" => "Кузова",
//        "url" => $lTransportBodyList->getData()["uri"],
//        "more_url" => ($lTransportBodyEdit->isSuccess()) ? [$lTransportBodyEdit->getData()["uri"]] : []
//    ];
//}

//$lTransportBrand = (new \Local\Core\Inner\AdminHelper\Reference\Transport\Brand\AdminList)->getAdminUri();
//if ($lTransportBrand->isSuccess()) {
//    $lTransportBrandEdit = (new \Local\Core\Inner\AdminHelper\Reference\Transport\Brand\AdminEdit())->getAdminUri();
//    $arTransportReference[] = [
//        "text" => "Бренды (марки)",
//        "url" => $lTransportBrand->getData()["uri"],
//        "more_url" => ($lTransportBrandEdit->isSuccess()) ? [$lTransportBrandEdit->getData()["uri"]] : []
//    ];
//}

//if (!empty($arTransportReference)) {
//    $aMenu[] = [
//        "parent_menu" => "global_menu_content",
//        "text" => "Справочники транспорта",
//        "items_id" => "ref_transport",
//        "items" => $arTransportReference,
//        "icon" => "iblock_menu_icon_types",
//        "sort" => 1000
//    ];
//}



//if( class_exists(\Local\Core\Inner\Module\FeedExport\View\AdminList::class) )
//{
//    $lModuleFeedExport = ( new \Local\Core\Inner\Module\FeedExport\View\AdminList() )->getAdminUri();
//    if( $lModuleFeedExport->isSuccess() )
//    {
//        $lModuleFeedExportEdit =  ( new \Local\Core\Inner\Module\FeedExport\View\AdminEdit() )->getAdminUri();
//
//        $aMenu[] = [
//            "parent_menu" => "global_menu_marketing",
//            "text" => "Генерация фидов",
//            'url' => $lModuleFeedExport->getData()['uri'],
//            "more_url" => ($lModuleFeedExportEdit->isSuccess()) ? [$lModuleFeedExportEdit->getData()["uri"]] : [],
//            "items_id" => "module_feed_export",
//            "icon" => "iblock_menu_icon_types",
//            "sort" => 1
//        ];
//    }
//
//}



if( class_exists(\Local\Core\Inner\AdminHelper\Data\Company\AdminList::class) )
{
    $lDataCompanyList = ( new \Local\Core\Inner\AdminHelper\Data\Company\AdminList() )->getAdminUri();
    if( $lDataCompanyList->isSuccess() )
    {
        $lDataCompanyEdit =  ( new \Local\Core\Inner\AdminHelper\Data\Company\AdminEdit() )->getAdminUri();

        $aMenu[] = [
            "parent_menu" => "global_menu_local_core",
            "text" => "Компании",
            'url' => $lDataCompanyList->getData()['uri'],
            "more_url" => ($lDataCompanyEdit->isSuccess()) ? [$lDataCompanyEdit->getData()["uri"]] : [],
            "items_id" => "data_company",
            "icon" => "fileman_menu_icon",
            "sort" => 1
        ];
    }

}


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

return (!empty($aMenu)) ? $aMenu : false;

