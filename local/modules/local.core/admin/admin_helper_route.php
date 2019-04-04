<?php
/**
 * @var $page \Local\Core\Inner\AdminHelper\ListBase|\Local\Core\Inner\AdminHelper\EditBase
 */

use Bitrix\Main\
{Application, Loader};

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

/**
 * Правила для построения страничек админки
 * "код_страницы" => [
 *    "действие" => Исполняемый класс унаследованный от \Local\Core\Inner\AdminHelper\EditBase или
 * \Local\Core\Inner\AdminHelper\ListBase
 * ]
 */
$arRouteRules = [
    // Model\Data
    "model_data_company" => [
        "list" => \Local\Core\Inner\AdminHelper\Data\Company\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Data\Company\AdminEdit::class,
    ],
    "model_data_store" => [
        "list" => \Local\Core\Inner\AdminHelper\Data\Store\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Data\Store\AdminEdit::class,
    ],

    // Model\Reference
    "model_reference_measure" => [
        "list" => \Local\Core\Inner\AdminHelper\Reference\Measure\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Reference\Measure\AdminEdit::class,
    ],
    "model_reference_currency" => [
        "list" => \Local\Core\Inner\AdminHelper\Reference\Currency\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Reference\Currency\AdminEdit::class,
    ],
    "model_reference_country" => [
        "list" => \Local\Core\Inner\AdminHelper\Reference\Country\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Reference\Country\AdminEdit::class,
    ],

    // Tariff
    "model_data_tariff" => [
        "list" => \Local\Core\Inner\AdminHelper\Data\Tariff\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Data\Tariff\AdminEdit::class,
    ],

    // Balance
    "model_data_balance_log" => [
        "list" => \Local\Core\Inner\AdminHelper\Data\BalanceLog\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Data\BalanceLog\AdminEdit::class,
    ],
    "model_data_attempts_top_up_balance_log" => [
        "list" => \Local\Core\Inner\AdminHelper\Data\AttemptsTopUpBalanceLog\AdminList::class,
        "edit" => \Local\Core\Inner\AdminHelper\Data\AttemptsTopUpBalanceLog\AdminEdit::class,
    ],

];


$context = Application::getInstance()
    ->getContext();
$request = $context->getRequest();
$response = $context->getResponse();

if (Loader::includeModule("local.core")) {
    $entity = $request->getQuery("adminEntity");
    $action = $request->getQuery("adminAction");
}

if (isset($entity) && isset($arRouteRules[$entity]) && isset($arRouteRules[$entity][$action]) && class_exists($arRouteRules[$entity][$action])) {
    $page = new $arRouteRules[$entity][$action]();
    $page->render();
} else {

    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
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
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");

}
