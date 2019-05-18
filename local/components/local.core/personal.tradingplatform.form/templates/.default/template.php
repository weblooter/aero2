<?
/**
 * @var array                                 $arParams
 * @var array                                 $arResult
 * @var \PersonalTradingPlatformFormComponent $component
 * @var CBitrixComponentTemplate              $this
 * @var string                                $templateName
 * @var string                                $componentPath
 * @var string                                $templateFolder
 * @global CMain                              $APPLICATION
 */

?>
<? if (empty($arResult['OB_HANDLER']) && empty($arResult['STATUS'])): ?>
    <div class="row">
        <? foreach (Local\Core\Inner\TradingPlatform\Factory::getFactoryList() as $k => $v): ?>
            <div class="col-sm-6 col-md-3 mb-4">
                <a class="quick-stats__item d-block mb-0 bg-secondary" href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#HANDLER#' => $k])?>">
                    <div class="quick-stats__info text-center">
                        <img src="/local/templates/.default/assets/img/tradingplatform-icons/<?=$k?>.svg" class="d-block m-auto mb-3 tradingplatform__img" />
                        <h5 class="mt-3 mb-0 text-dark tradingplatform__title">
                            <?=$v?>
                        </h5>
                    </div>
                </a>
            </div>
        <? endforeach; ?>
    </div>

    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail',
        ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']])?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться в магазин</a>
<? elseif (empty($arResult['OB_HANDLER']) && !empty($arResult['STATUS'])): ?>
    <? if (
        $arResult['STATUS'] == 'ERROR'
        || $arResult['STATUS'] == 'HANDLER_NOT_FOUND'
    ): ?>
        <div class="alert alert-danger" role="alert">
            <?=$arResult['ERROR_TEXT'];?>
        </div>

        <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail',
            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']])?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться в магазин</a>
    <? endif; ?>
<? else: ?>
    <div class="card">
        <div class="card-body">

            <? if ($arResult['STATUS'] == 'TP_NOT_FOUNT'): ?>
                <div class="alert alert-danger">
                    <?=$arResult['ERROR_TEXT'];?>
                </div>
            <? elseif ($arResult['STATUS'] == 'HANDLER_NOT_FOUND'): ?>
                <div class="alert alert-danger">
                    <?=$arResult['ERROR_TEXT'];?>
                </div>
            <? elseif ($arResult['STATUS'] == 'ADD_SUCCESS'): ?>
            <?
            $strEditRoute = \Local\Core\Inner\Route::getRouteTo('store', 'detail',
                ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']]);
            ?>
                <div class="alert alert-success">
                    Торговая площадка успешно создана!
                </div>

                <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']])?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться в магазин</a>
                <script type="text/javascript">
                    setTimeout(function () {
                        location.href = '<?=$strEditRoute?>';
                    }, 1500);
                </script>
            <? else: ?>

            <? if ($arResult['STATUS'] == 'UPDATE_SUCCESS'): ?>
                <div class="alert alert-success">
                    Данные успешно обновлены.<br/>
                    <br/>
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']])?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться в магазин</a>
                </div>
            <? elseif ($arResult['STATUS'] == 'ERROR'): ?>
                <div class="alert alert-danger">
                    <?=$arResult['ERROR_TEXT'];?>
                </div>
            <? endif; ?>

            <?
            $strFormAction = '';

            if ($arParams['TP_ID'] > 0) {
                $strFormAction = \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'edit',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arParams['TP_ID']]);
            } else {
                $strFormAction = \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add', [
                    '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                    '#STORE_ID#' => $arParams['STORE_ID'],
                    '#HANDLER#' => \Bitrix\Main\Application::getInstance()
                        ->getContext()
                        ->getRequest()
                        ->get('handler')
                ]);
            }
            ?>

            <?
            if ($arParams['TP_ID'] > 0)
            {
            $obHandler = $arResult['OB_HANDLER'];
            if ($obHandler instanceof Local\Core\Inner\TradingPlatform\Handler\AbstractHandler) {
            $obCheckResult = $obHandler->isRulesTradingPlatformCorrectFilled();
            if (!$obCheckResult->isSuccess())
            {
            ?>
                <div class="alert alert-danger" role="alert">
                    <?=implode('<br/>', $obCheckResult->getErrorMessages())?>
                </div>
            <?
            }
            }
            }
            ?>

                <form action="<?=$strFormAction?>" method="post" id="tradingplatformform">
                    <div class="page-loader d-block">
                        <div class="page-loader__spinner">
                            <svg viewBox="25 25 50 50">
                                <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
                            </svg>
                        </div>
                    </div>

                    <?=bitrix_sessid_post();?>
                    <input type="hidden" name="TP_DATA[HANDLER]" value="<?=($arResult['TP_DATA']['HANDLER']) ? $arResult['TP_DATA']['HANDLER'] : \Bitrix\Main\Application::getInstance()
                        ->getContext()
                        ->getRequest()
                        ->get('handler')?>" />
                    <? if ($arParams['TP_ID'] > 0): ?>
                        <input type="hidden" name="TP_DATA[ID]" value="<?=$arParams['TP_ID']?>" />
                    <? endif; ?>
                    <input type="hidden" name="TP_DATA[STORE_ID]" value="<?=$arParams['STORE_ID']?>" />

                    <?
                    (new \Local\Core\Inner\TradingPlatform\Field\Header())->setValue('Базовые настройки')
                        ->printRow();
                    ?>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="font-weight-bold">Название * :</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="TP_DATA[NAME]" class="form-control" value="<?=$arResult['TP_DATA']['NAME']?>" required placeholder="Площадка №17" />
                            </div>
                        </div>
                    </div>
                    <?
                    (new \Local\Core\Inner\TradingPlatform\Field\Condition())
                        ->setTitle('Фильт товаров')
                        ->setStoreId($arParams['STORE_ID'])
                        ->setName('TP_DATA[PRODUCT_FILTER]')
                        ->setValue($arResult['TP_DATA']['PRODUCT_FILTER'] ?? [])
                        ->printRow();
                    ?>

                    <div data-handler-fields>
                        <?
                        $obHandler = $arResult['OB_HANDLER'];
                        if ($obHandler instanceof Local\Core\Inner\TradingPlatform\Handler\AbstractHandler) {
                            $obHandler->printFormFields();
                        }
                        ?>
                    </div>

                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail',
                        ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']])?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться в магазин</a>
                    &ensp;
                    <button type="submit" class="btn btn-warning" name="SAVE" value="Y"><?=($arParams['TP_ID'] > 0) ? 'Обновить' : 'Сохранить'?></button>
                </form>

            <? if ($arResult['STATUS'] == 'UPDATE_SUCCESS'): ?>
                <div class="alert alert-success mt-4" role="alert">
                    Данные успешно обновлены
                </div>
            <? elseif ($arResult['STATUS'] == 'ERROR'): ?>
                <div class="alert alert-danger mt-4" role="alert">
                    <?=$arResult['ERROR_TEXT'];?>
                </div>
            <? endif; ?>

                <script type="text/javascript">
                    LocalCore.initFormComponents();
                    <?
                    $arOptions = (new \Local\Core\Inner\TradingPlatform\Field\Resource())
                        ->setStoreId($arParams['STORE_ID'])
                        ->getSourceOptionsToJs();
                    ?>
                    PersonalTradingplatformFormComponent.setBuilderOptions(JSON.parse('<?=$arOptions?>'));
                    window.onload = function(){
                        PersonalTradingplatformFormComponent.hideLoading();
                    };
                </script>

            <? endif; ?>
        </div>
    </div>
<? endif; ?>
