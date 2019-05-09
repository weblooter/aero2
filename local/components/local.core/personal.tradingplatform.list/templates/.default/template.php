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
<div class="card">
    <div class="card-body">
        <h4 class="card-title">
            Торговые площадки
        </h4>
        <?
        if( !\Local\Core\Inner\Store\Base::hasSuccessImport( $arParams['STORE_ID'] ) )
        {
            ?>
            У магазина не было еще ни одного успешного импорта. Создание торговых площадок возможно только после того, как данные по Вашим товарам будут импортированы в нашу систему.
            <?
        }
        else
        {
            ?>
            <div class="actions">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#HANDLER#' => ''])?>" class="actions__item zmdi zmdi-plus" title="Добавить торговую площадку"></a>
            </div>
            <?
        }
        ?>

        <?if( !empty( $arResult['ITEMS'] ) ):?>

            <div class="accordion" role="tablist">
                <? foreach ($arResult['ITEMS'] as $arItem): ?>

                    <?
                    $strStatusClass = null;
                    $strStatus = null;
                    $strActiveStatusIcon = null;
                    $strErrorHtml = null;


                    $obTp = new \Local\Core\Inner\TradingPlatform\TradingPlatform();
                    $obTp->load($arItem['ID']);

                    $obHandler = $obTp->getHandler();
                    $obCheckRes = $obHandler->isRulesTradingPlatformCorrectFilled();

                    if( $arItem['ACTIVE'] == 'Y' )
                    {
                        $strStatusClass = 'alert-success';
                        $strActiveStatusIcon = 'zmdi zmdi-play';
                        $strStatus = '<span class="badge badge-success">Активна</span>';
                    }
                    else
                    {

                        if ($arItem['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime) {
                            if ($arItem['PAYED_TO']->getTimestamp() <= time()) {
                                $strStatusClass = 'alert-warning';
                                $strActiveStatusIcon = 'zmdi zmdi-stop';
                                $strStatus = '<span class="badge badge-warning">Деактивирована</span>';

                            } else {
                                $strStatusClass = 'alert-secondary';
                                $strActiveStatusIcon = 'zmdi zmdi-pause';
                                $strStatus = '<span class="badge badge-secondary">Остановлен</span>';
                            }
                        } else {
                            $strStatusClass = 'alert-warning';
                            $strActiveStatusIcon = 'zmdi zmdi-stop';
                            $strStatus = '<span class="badge badge-warning">Ожидает активации</span>';
                        }
                    }

                    if( !$obCheckRes->isSuccess() )
                    {
                        $strStatusClass = 'alert-danger';
                        $strActiveStatusIcon = 'zmdi zmdi-alert-circle-o';
                        $strErrorHtml .= (!empty( $strErrorHtml ) ? '<br/><br/>' : '').'<b>Ошибка валидации торговой площадки:</b><br/>'.implode('<br/>', $obCheckRes->getErrorMessages()).'<br/><br/><b>До момента устранения ошибок экспортный файл обновляться не будет!</b>';
                    }


                    $rsLastLog = \Local\Core\Model\Data\TradingPlatformExportLogTable::getList([
                        'filter' => ['TP_ID' => $arItem['ID']],
                        'order' => ['DATE_CREATE' => 'DESC'],
                        'limit' => 1
                    ]);
                    $arLastLog = $rsLastLog->fetch();

                    if( !empty( $arLastLog ) )
                    {

                        if( $arLastLog['RESULT'] == 'ER' )
                        {

                            $strStatusClass = 'alert-danger';
                            $strActiveStatusIcon = 'zmdi zmdi-alert-circle-o';

                            if( !empty( $arLastLog['ERROR_TEXT'] ) )
                            {
                                $strErrorHtml .= (!empty( $strErrorHtml ) ? '<br/><br/>' : '').'<b>Во время формирования экспортного файла были обнаружены ошибки:</b><br/>'.$arLastLog['ERROR_TEXT'];
                            }
                        }
                    }

                    ?>

                    <div class="card mb-0">
                        <div class="card-header alert <?=$strStatusClass?> mb-0" role="tab">
                            <a href="javascript:void(0)" data-toggle="collapse" data-target="#collapseTP<?=$arItem['ID']?>">
                                <i class="<?=$strActiveStatusIcon?> lead"></i> &ensp; <?=$arItem['NAME']?>
                            </a>

                            <div class="actions">
                                <div class="dropdown actions__item">
                                    <i data-toggle="dropdown" class="zmdi zmdi-more-vert"></i>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <?
                                        if ($arItem['ACTIVE'] == 'Y') {
                                            ?>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="PersonalTradingplatformListComponent.deactivateTP('<?=$arItem['ID']?>', '<?=htmlspecialchars($arItem['NAME'])?>')">
                                                <i class="zmdi zmdi-pause"></i> &nbsp;
                                                Остановить
                                            </a>
                                            <?
                                        } else {
                                            ?>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="PersonalTradingplatformListComponent.activateTP('<?=$arItem['ID']?>', '<?=htmlspecialchars($arItem['NAME'])?>')">
                                                <i class="zmdi zmdi-play"></i> &nbsp;
                                                Активировать
                                            </a>
                                            <?
                                        }
                                        ?>
                                        <a class="dropdown-item" href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'edit',
                                            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#TP_ID#' => $arItem['ID']])?>">
                                            <i class="zmdi zmdi-edit"></i> &nbsp;
                                            Изменить
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0)" onclick="PersonalTradingplatformListComponent.deleteTP('<?=$arItem['ID']?>', '<?=htmlspecialchars($arItem['NAME'])?>')" >
                                            <i class="zmdi zmdi-delete"></i> &nbsp;
                                            Удалить
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="collapseTP<?=$arItem['ID']?>" class="collapse">
                            <div class="card-body pb-3">
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <th class="w-50">
                                            Площадка:
                                        </th>
                                        <td class="w-50">
                                            <?=\Local\Core\Inner\TradingPlatform\Factory::getFactoryList()[$arItem['HANDLER']]?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Статус:
                                        </th>
                                        <td>
                                            <?=$strStatus?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Оплачена до:
                                        </th>
                                        <td>
                                            <?=(($arItem['PAYED_TO'] instanceof \Bitrix\Main\Type\DateTime) ? $arItem['PAYED_TO']->format('Y.m.d H:i') : 'Активация не производилась')?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            Ссылка на файл экспорта:
                                        </th>
                                        <td>
                                            <a href="javascript:void(0)" class="btn btn-outline-secondary" onclick="PersonalTradingplatformListComponent.showExportLink('<?=htmlspecialchars('https://robofeed.ru'.\Local\Core\Inner\TradingPlatform\Base::getExportFileLink($arItem['ID']))?>')">Получить ссылку</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>


                                <h4 class="p-3 mb-4 bg-secondary text-dark"><small>Данные последней попытки формирования файла</small></h4>

                                <?
                                if( !empty( $arLastLog ) )
                                {

                                    switch ($arLastLog['RESULT'])
                                    {
                                        case 'SU':
                                            ?>

                                            <table class="table table-striped mb-4">
                                                <tbody>
                                                <tr>
                                                    <th class="w-50">
                                                        Дата попытки формирования файла:
                                                    </th>
                                                    <td>
                                                        <?=( ($arLastLog['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime) ? $arLastLog['DATE_CREATE']->format('Y.m.d H:i:s') : '-' )?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Результат:
                                                    </th>
                                                    <td>
                                                        <span class="badge badge-success">Успех</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Товаров в магазине на момент формирования файла:
                                                    </th>
                                                    <td>
                                                        <?=number_format($arLastLog['PRODUCTS_TOTAL'], 0, '.', ' ')?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Товаров экспортировано:
                                                    </th>
                                                    <td>
                                                        <?=number_format($arLastLog['PRODUCTS_EXPORTED'], 0, '.', ' ')?>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <?
                                            break;
                                        case 'ER':
                                            ?>

                                            <table class="table table-striped mb-4">
                                                <tbody>
                                                <tr>
                                                    <th class="w-50">
                                                        Дата попытки формирования файла:
                                                    </th>
                                                    <td>
                                                        <?=( ($arLastLog['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime) ? $arLastLog['DATE_CREATE']->format('Y.m.d H:i:s') : '-' )?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        Результат:
                                                    </th>
                                                    <td>
                                                        <span class="badge badge-danger">Ошибка</span>
                                                    </td>
                                                </tr>
                                                <?if( $arLastLog['PRODUCTS_TOTAL'] > 0 ):?>
                                                    <tr>
                                                        <th>
                                                            Всего товаров в магазине было:
                                                        </th>
                                                        <td>
                                                            <?=number_format($arLastLog['PRODUCTS_TOTAL'], 0, '.', ' ')?>
                                                        </td>
                                                    </tr>
                                                <?endif;?>
                                                </tbody>
                                            </table>
                                            <?
                                            break;
                                    }
                                }
                                else
                                {
                                    ?>
                                    <table class="table table-striped mb-4">
                                        <tbody>
                                        <tr>
                                            <td>
                                                Экспортный файл еще ни разу не формировался.
                                            </td>
                                        </tr>
                                    </table>
                                    <?
                                }
                                ?>

                                <?if( !is_null($strErrorHtml) && !empty( $strErrorHtml ) ):?>
                                    <div class="alert alert-danger"><?=$strErrorHtml?></div>
                                <?endif;?>

                            </div>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
        <?else:?>
            У магазина нет ни одной торговой площадки.<br/>
            <br/>
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('tradingplatform', 'add',
                ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID'], '#HANDLER#' => ''])?>" class="btn btn-outline-secondary">Добавить торговую площадку</a>
        <?endif;?>

    </div>
</div>