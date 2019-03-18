<?
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php" );
$APPLICATION->SetTitle("Title");

$intCompanyId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('COMPANY_ID');
$intStoreId = \Bitrix\Main\Application::getInstance()
    ->getContext()
    ->getRequest()
    ->get('STORE_ID');
?>
    <div class="container-fluid">
        <div class="row">

            <div class="col-12">

                <?
                $APPLICATION->IncludeComponent(
                    "local.core:personal.store.edit",
                    ".default",
                    array(
                        "COMPANY_ID" => $intCompanyId,
                        "STORE_ID" => $intStoreId,
                        "AJAX_MODE" => "Y",
                        "COMPONENT_TEMPLATE" => ".default",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "N",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_ADDITIONAL" => ""
                    ),
                    false
                );
                ?>

            </div>

        </div>
    </div>
<? require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php" ); ?>