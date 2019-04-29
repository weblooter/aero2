<?
/**
 * @var array                                 $arParams
 * @var array                                 $arResult
 * @var \PersonalTradingPlatformListComponent $component
 * @var CBitrixComponentTemplate              $this
 * @var string                                $templateName
 * @var string                                $componentPath
 * @var string                                $templateFolder
 * @global CMain                              $APPLICATION
 */

?>
<div class="row">
    <div class="col-xs-12">
        <?
        if( \Local\Core\Inner\Store\Base::hasSuccessImport( $arParams['STORE_ID'] ) )
        {
            ?>
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
            <?
        }
        else
        {
            ?>
            <div class="alert alert-danger">
                У магазина не было еще ни одного успешного импорта. Создание торговых площадок возможно только после того, как данные по Вашим товарам будут импортированны в нашу систему.
            </div>
            <?
        }
        ?>

        <? foreach ($arResult['ITEMS'] as $arItem): ?>
            <?
            $obTp = new \Local\Core\Inner\TradingPlatform\TradingPlatform();
            $obTp->load($arItem['ID']);

            $strClass = '';
            $strIcon = '';
            $strStatus = '';
            $strError = '';

            try {
                $obHandler = $obTp->getHandler();
                $obCheckRes = $obHandler->isRulesTradingPlatformCorrectFilled();

                if ($arItem['ACTIVE'] == 'Y') {
                    $strClass = 'alert-success';
                    $strIcon = 'battery-charging';
                    $strStatus = 'Активен. Оплачен до '.(($arItem['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime) ? $arItem['PAYED_TO']->format('Y.m.d H:i') : '-');
                } else {
                    if ($arItem['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime) {
                        if ($arItem['PAYED_TO']->getTimestamp() <= time()) {
                            $strClass = 'alert-danger';
                            $strIcon = 'battery-dead';
                            $strStatus = 'Деактивирован.';
                        } else {
                            $strClass = 'alert-warning';
                            $strIcon = 'battery-full';
                            $strStatus = 'Деактивирован. Оплачен до '.$arItem['PAYED_TO']->format('Y.m.d H:i:s');
                        }
                    } else {
                        $strClass = 'alert-warning';
                        $strIcon = 'battery-full';
                        $strStatus = 'Ожидает активации.';
                    }
                }

                if( !$obCheckRes->isSuccess() )
                {
                    $strClass = 'alert-danger';
                    $strIcon = 'battery-dead';
                    $strError = '<br/><b>Ошибка валидации торговой площадки:</b><br/>'.implode('<br/>', $obCheckRes->getErrorMessages()).'<br/><b>До момента устранения ошибок экспортный файл обновляться не будет!</b>';
                }

            } catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e) {
                $strClass = 'alert-danger';
                $strIcon = 'battery-dead';
                $strStatus = 'Не удалось получить обработчик торговой площадки.';
            } catch (\Exception $e) {
                $strClass = 'alert-danger';
                $strIcon = 'battery-dead';
                $strStatus = 'Ошибка.';
                $strError = '<br/>'.(!empty($e->getMessage())) ? '<br/>'.$e->getMessage() : '';
            }

            $strEditLink = \Local\Core\Inner\Route::getRouteTo('tradingplatform', 'edit',
                ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arItem['ID']])
            ?>
            <div class="alert <?=$strClass?>">
                <ion-icon name="<?=$strIcon?>"></ion-icon>
                <a href="<?=$strEditLink?>"><?=$arItem['NAME']?> [<?=\Local\Core\Inner\TradingPlatform\Factory::getFactoryList()[$arItem['HANDLER']]?>]</a>
                <br /><?=$strStatus?>
                <?=$strError?>
                <?
                $rsLastLog = \Local\Core\Model\Data\TradingPlatformExportLogTable::getList([
                    'filter' => ['TP_ID' => $arItem['ID']],
                    'order' => ['DATE_CREATE' => 'DESC'],
                    'limit' => 1
                ]);
                if(
                    $arItem['ACTIVE'] == 'Y'
                    || (
                        $arItem['ACTIVE'] == 'N'
                        && $rsLastLog->getSelectedRowsCount() > 0
                    )
                )
                {
                    if( $rsLastLog->getSelectedRowsCount() > 0 )
                    {
                        $arLastLog = $rsLastLog->fetch();
                        switch ($arLastLog['RESULT'])
                        {
                            case 'SU':
                                ?>
                                <div class="alert alert-success">
                                    <b>Ссылка на файл экспорта:</b> https://robofeed.ru<?=\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($arItem['ID']);?><br/>
                                    <b>Последний раз экспортный файл был сформирован:</b> <?=( ($arLastLog['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime) ? $arLastLog['DATE_CREATE']->format('Y.m.d H:i:s') : '-' )?>
                                </div>
                                <?
                                break;
                            case 'ER':
                                ?>
                                <div class="alert alert-danger">
                                    <b>Ссылка на файл экспорта:</b> https://robofeed.ru<?=\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($arItem['ID']);?><br/>
                                    Дата попытки формирования файла: <?=( ($arLastLog['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime) ? $arLastLog['DATE_CREATE']->format('Y.m.d H:i:s') : '-' )?><br/>
                                    <b>Текст ошибки:</b><br/>
                                    <?=$arLastLog['ERROR_TEXT']?>
                                </div>
                                <?
                                break;
                        }
                    }
                    else
                    {
                        ?>
                        <div class="alert alert-warning">
                            Ожидайте создайте экспортного файла.
                        </div>
                        <?
                    }
                }
                else
                {
                    echo '<br/><br/>';
                }
                ?>
                <div class="dropdown">
                    <button class="btn btn-warning" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Действие
                    </button>
                    <ul class="dropdown-menu">
                        <?
                        if ($arItem['ACTIVE'] == 'Y') {
                            ?>
                            <li>
                                <a href="javascript:void(0)" onclick="PersonalTradingplatformListComponent.deactivateTP('<?=$arItem['ID']?>')">
                                    <ion-icon name="pause"></ion-icon>
                                    Остановить</a>
                            </li>
                            <?
                        } else {
                            ?>
                            <li>
                                <a href="javascript:void(0)" onclick="PersonalTradingplatformListComponent.activateTP('<?=$arItem['ID']?>')">
                                    <ion-icon name="play"></ion-icon>
                                    Активировать</a>
                            </li>
                            <?
                        }
                        ?>
                        <li>
                            <a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'edit',
                                ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arItem['ID']])?>" onclick="">
                                <ion-icon name="create"></ion-icon>
                                Редактировать</a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" onclick="PersonalTradingplatformListComponent.deleteTP('<?=$arItem['ID']?>')">
                                <ion-icon name="trash"></ion-icon>
                                Удалить</a>
                        </li>
                    </ul>
                </div>

            </div>
        <? endforeach; ?>
    </div>
</div>