<?
/**
 * @global CMain                              $APPLICATION
 * @var array                                 $arParams
 * @var array                                 $arResult
 * @var \PersonalTradingPlatformListComponent $component
 * @var CBitrixComponentTemplate              $this
 * @var string                                $templateName
 * @var string                                $componentPath
 * @var string                                $templateFolder
 */

?>
<div class="row">
    <div class="col-6">
        <div>
            <div class="btn-group" role="group">
                <button id="btnGroupDrop21" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Добавить торговую площадку
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop21">
                    <?
                    foreach (Local\Core\Inner\TradingPlatform\Factory::getFactoryList() as $k => $v) {
                        ?>
                        <a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#HANDLER#' => $k])?>" class="dropdown-item "><?=$v?></a>
                        <?
                    }
                    ?>
                </div>
            </div>
        </div>
        <br />

        <ul class="list-group">
            <? foreach ($arResult['ITEMS'] as $arItem): ?>
                <?
                $strClass = '';
                $strIcon = '';
                if ($arItem['ACTIVE'] == 'Y') {
                    $strClass = 'list-group-item-success';
                    $strIcon = 'battery-charging';
                } else {
                    if ($arItem['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime) {
                        if ($arItem['PAYED_TO']->getTimestamp() <= time()) {
                            $strClass = 'list-group-item-danger';
                            $strIcon = 'battery-dead';
                        }
                    } else {
                        $strClass = 'list-group-item-warning';
                        $strIcon = 'battery-full';
                    }
                }

                $strDetailLink = \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'detail',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arItem['ID']])
                ?>
                <li class="list-group-item <?=$strClass?>">
                    <ion-icon name="<?=$strIcon?>"></ion-icon>
                    <a href="<?=$strDetailLink?>"><?=$arItem['NAME']?> [<?=\Local\Core\Inner\TradingPlatform\Factory::getFactoryList()[$arItem['HANDLER']]?>]</a> <?=($arItem['PAYED_TO'] instanceof
                                                                                                                                                                      \Bitrix\Main\Type\DateTime) ? 'Оплачен до '
                                                                                                                                                                                                    .$arItem['PAYED_TO']->format('Y-m-d') : 'Готов к оплате и активации'?>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
</div>