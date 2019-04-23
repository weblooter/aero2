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
    <div class="col-xs-6">
        <div>
            <div class="dropdown">
                <button type="button" class="btn btn-warning" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Добавить торговую площадку
                </button>
                <ul class="dropdown-menu">
                    <?
                    foreach (Local\Core\Inner\TradingPlatform\Factory::getFactoryList() as $k => $v) {
                        ?>
                        <li><a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add',
                            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#HANDLER#' => $k])?>"><?=$v?></a></li>
                        <?
                    }
                    ?>
                </ul>
            </div>
        </div>
        <br />

        <ul class="list-group">
            <? foreach ($arResult['ITEMS'] as $arItem): ?>
                <?
                $obTp = new \Local\Core\Inner\TradingPlatform\TradingPlatform();
                $obTp->load($arItem['ID']);

                $strClass = '';
                $strIcon = '';
                $strStatus = '';
                $strError = '';

                try
                {
                    $obHandler = $obTp->getHandler();
                    $obCheckRes = $obHandler->isRulesTradingPlatformCorrectFilled();

                    if( !$obCheckRes->isSuccess() )
                    {
                        $strClass = 'list-group-item-danger';
                        $strIcon = 'battery-dead';
                        $strStatus = 'Ошибка валидации';
                        $strError = '<br/>'.implode('<br/>', $obCheckRes->getErrorMessages());
                    }
                    elseif ($arItem['ACTIVE'] == 'Y') {
                        $strClass = 'list-group-item-success';
                        $strIcon = 'battery-charging';
                        $strStatus = 'Активен. Оплачен до '.$arItem['PAYED_TO']->format('Y.m.d');
                    }
                    elseif( $arItem['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime ) {
                        if ( $arItem['PAYED_TO']->getTimestamp() <= time() ) {
                            $strClass = 'list-group-item-danger';
                            $strIcon = 'battery-dead';
                            $strStatus = 'Необходимо пополнить баланс';
                        } else {
                            $strClass = 'list-group-item-warning';
                            $strIcon = 'battery-full';
                            $strStatus = 'Деактивирован';
                        }
                    }
                    else
                    {
                        $strClass = 'list-group-item-warning';
                        $strIcon = 'battery-full';
                        $strStatus = 'Ожидает активации';
                    }
                }
                catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e)
                {
                    $strClass = 'list-group-item-danger';
                    $strIcon = 'battery-dead';
                    $strStatus = 'Не удалось получить обработчик';
                }
                catch (\Exception $e)
                {
                    $strClass = 'list-group-item-danger';
                    $strIcon = 'battery-dead';
                    $strStatus = 'Ошибка';
                    $strError = ( !empty( $e->getMessage() ) ) ? '<br/>'.$e->getMessage() : '';
                }

                $strEditLink = \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'edit',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arItem['ID']])
                ?>
                <li class="list-group-item <?=$strClass?>">
                    <ion-icon name="<?=$strIcon?>"></ion-icon>
                    <a href="<?=$strEditLink?>"><?=$arItem['NAME']?> [<?=\Local\Core\Inner\TradingPlatform\Factory::getFactoryList()[$arItem['HANDLER']]?>]</a> <?=$strStatus?>
                    <?=$strError?>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
</div>