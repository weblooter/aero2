<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$intCompanyId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_ID');
?>
<div class="container-fluid">
    <div class="row">

        <div class="col-12">
            <?
            $GLOBALS['APPLICATION']->IncludeComponent(
                'local.core:personal.site.add',
                '.default',
                [
                    'COMPANY_ID' => $intCompanyId,
                    'AJAX_MODE' => 'Y',
                ]
            );
            ?>
        </div>

    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>