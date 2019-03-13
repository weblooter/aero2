<?php
/**
 * @var $page \Local\Core\Inner\AdminHelper\ListBase|\Local\Core\Inner\AdminHelper\EditBase
 */

use Bitrix\Main\
{Application, Loader};

require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php" );

/**
 * Правила для построения страничек админки
 * "код_страницы" => [
 *    "действие" => Исполняемый класс унаследованный от \Local\Core\Inner\AdminHelper\EditBase или
 * \Local\Core\Inner\AdminHelper\ListBase
 * ]
 */
$arRouteRules = [
    "data_company" => [
        "list" => \Local\Core\Inner\AdminHelper\Data\Company\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Data\Company\AdminEdit::class,
    ],
    //    "reference_organization" => [
    //        "list" => \Local\Core\Inner\AdminHelper\Reference\Organization\AdminList::class,
    //        "edit" => \Local\Core\Inner\AdminHelper\Reference\Organization\AdminEdit::class,
    //    ],
    //    "reference_useraddress" => [
    //        "list" => \Local\Core\Inner\AdminHelper\Reference\UserAddress\AdminList::class,
    //        "edit" => \Local\Core\Inner\AdminHelper\Reference\UserAddress\AdminEdit::class,
    //    ],

];


$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$response = $context->getResponse();

if( Loader::includeModule("local.core") )
{
    $entity = $request->getQuery("adminEntity");
    $action = $request->getQuery("adminAction");
}

if( isset($entity) && isset($arRouteRules[$entity]) && isset($arRouteRules[$entity][$action]) && class_exists($arRouteRules[$entity][$action]) )
{
    $page = new $arRouteRules[$entity][$action];
    $page->render();
}
else
{

    require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php" );
    define("ERROR_404", "Y");
    define("BX_ADMIN_SECTION_404", "Y");
    $response->setStatus("404 Not Found");
    IncludeModuleLangFile(__FILE__);
    $APPLICATION->SetTitle(GetMessage("404_title")); ?>

    <div class="adm-404-block">
        <div class="adm-404-text1">
            <?
            echo GetMessage("404_header") ?>
        </div>
        <div class="adm-404-text2"><?
            echo GetMessage("404_message") ?></div>
        <div class="adm-404-footer"></div>
    </div>
    <?
    require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php" );

}
