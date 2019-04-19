<?
/**
 * @global CMain                              $APPLICATION
 * @var array                                 $arParams
 * @var array                                 $arResult
 * @var \PersonalTradingPlatformFormComponent $component
 * @var CBitrixComponentTemplate              $this
 * @var string                                $templateName
 * @var string                                $componentPath
 * @var string                                $templateFolder
 */

?>
<? if (empty($arResult['OB_HANDLER'])): ?>

    <h3>Выберите обработчик</h3>
    <ul>
        <? foreach (Local\Core\Inner\TradingPlatform\Factory::getFactoryList() as $k => $v): ?>
            <li><a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#HANDLER#' => $k])?>"><?=$v?></a></li>
        <? endforeach; ?>
    </ul>

<? else: ?>

    <? if ($arResult['STATUS'] == 'TP_NOT_FOUNT'): ?>
        <div class="alert alert-danger" role="alert">
            <?=$arResult['ERROR_TEXT'];?>
        </div>
    <? elseif ($arResult['STATUS'] == 'HANDLER_NOT_FOUND'): ?>
        <div class="alert alert-danger" role="alert">
            <?=$arResult['ERROR_TEXT'];?>
        </div>
    <? elseif ($arResult['STATUS'] == 'ADD_SUCCESS'): ?>
        <?
        $strDetailRoute = \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'detail',
            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arResult['ADD_ID']]);
        ?>
        <p>
            Торговая площадка успешно создана. Сейчас Вы будете переброшены на детальную страницу.<br />
            Есть этого не произошло - воспользуйтесь ссылкой <a href="<?=$strDetailRoute?>"><?=$strDetailRoute?></a>.
        </p>
        <script type="text/javascript">
            location.href = '<?=$strDetailRoute?>';
        </script>
    <? else: ?>

        <? if ($arResult['STATUS'] == 'ERROR'): ?>
            <div class="alert alert-danger" role="alert">
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
        <form action="<?=$strFormAction?>" method="post" id="tradingplatformform">
            <?=bitrix_sessid_post();?>
            <input type="hidden" name="TP_DATA[HANDLER]" value="<?=($arResult['TP_DATA']['HANDLER']) ? $arResult['TP_DATA']['HANDLER'] : \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->get('handler')?>" />
            <?if( $arParams['TP_ID'] > 0 ):?>
                <input type="hidden" name="TP_DATA[ID]" value="<?=$arParams['TP_ID']?>" />
            <?endif;?>
            <input type="hidden" name="TP_DATA[STORE_ID]" value="<?=$arParams['STORE_ID']?>" />

            <?
            (new \Local\Core\Inner\TradingPlatform\Field\Header())->setValue('Базовые настройки')
                ->printRow();
            ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-4 text-right">
                        <label><b>Название</b>:</label>
                    </div>
                    <div class="col-8 text-left">
                        <input type="text" name="TP_DATA[NAME]" class="form-control" value="<?=$arResult['TP_DATA']['NAME']?>" required />
                    </div>
                </div>
            </div>
            <?
            (new \Local\Core\Inner\TradingPlatform\Field\Condition())
                ->setTitle('Фильт товаров')
                ->setStoreId($arParams['STORE_ID'])
                ->setName('TP_DATA[PRODUCT_FILTER]')
                ->setValue($arResult['TP_DATA']['PRODUCT_FILTER'])
                ->printRow();
            ?>

            <?
            $obHandler = $arResult['OB_HANDLER'];
            if ($obHandler instanceof Local\Core\Inner\TradingPlatform\Handler\AbstractHandler) {
                $obHandler->printFormFields();
            }
            ?>
            <button type="submit" class="btn btn-warning" name="SAVE">Сохранить</button>
        </form>

        <? if ($arResult['STATUS'] == 'ERROR'): ?>
            <div class="alert alert-danger" role="alert">
                <?=$arResult['ERROR_TEXT'];?>
            </div>
        <? endif; ?>

        <script type="text/javascript">
            <?
            $arOptions = (new \Local\Core\Inner\TradingPlatform\Field\Resource())
                    ->setStoreId($arParams['STORE_ID'])
                    ->getSourceOptionsToJs();
            ?>
            PersonalTradingplatformFormComponent.setBuilderOptions(JSON.parse('<?=$arOptions?>'));
        </script>

    <? endif; ?>

<? endif; ?>
