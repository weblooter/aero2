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
        <?foreach (Local\Core\Inner\TradingPlatform\Factory::getFactoryList() as $k => $v):?>
            <li><a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#HANDLER#' => $k])?>"><?=$v?></a></li>
        <?endforeach;?>
    </ul>

<? else: ?>

    <? if ($arResult['STATUS'] == 'TP_NOT_FOUNT'): ?>
        <?=$arResult['ERROR_TEXT'];?>
    <? elseif ($arResult['STATUS'] == 'HANDLER_NOT_FOUND'): ?>
        <?=$arResult['ERROR_TEXT'];?>
    <? else: ?>

        <form action="<?=\Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->getRequestedPageDirectory()?>/" method="post">
            <?=bitrix_sessid_post();?>
            <?
            $obHandler = $arResult['OB_HANDLER'];
            if ($obHandler instanceof Local\Core\Inner\TradingPlatform\Handler\AbstractHandler) {
                $obHandler->printFormFields();
            }
            ?>
            <button type="submit" class="btn btn-warning" name="SAVE">Сохранить</button>
        </form>

    <? endif; ?>

<? endif; ?>
