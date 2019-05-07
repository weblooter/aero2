<?
/**
 * @global CMain                      $APPLICATION
 * @var array                         $arParams
 * @var array                         $arResult
 * @var \PersonalStoreDetailComponent $component
 * @var CBitrixComponentTemplate      $this
 * @var string                        $templateName
 * @var string                        $componentPath
 * @var string                        $templateFolder
 */

$obAssets = \Bitrix\Main\Page\Asset::getInstance();

$obAssets->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/flot/jquery.flot.js');
$obAssets->addJs(SITE_TEMPLATE_PATH.'/assets/bower_components/flot/jquery.flot.resize.js');
?>


<div class="row">
    <div class="col-lg-6">

        <div class="card">
            <div class="card-body pb-3">
                <h4 class="card-title">
                    Магазин
                </h4>
                <div class="actions">
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'edit', ['#COMPANY_ID#' => $arResult['ITEM']['COMPANY_ID'], '#STORE_ID#' => $arResult['ITEM']['ID']])?>" class="actions__item zmdi zmdi-settings zmdi-hc-fw" title="Редактировать настройки"></a>
                    <a href="javascript:void(0)" class="actions__item zmdi zmdi-delete zmdi-hc-fw" onclick="PersonalStoreDetailComponent.deleteStore('<?=$arResult['ITEM']['ID']?>', '<?=htmlspecialchars($arResult['ITEM']['NAME'])?>')" title="Удалить магазин"></a>
                </div>

                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th class="w-50">Сайт:</th>
                        <td><a href="<?=$arResult['ITEM']['DOMAIN']?>" target="_blank"><?=$arResult['ITEM']['DOMAIN']?></a></td>
                    </tr>

                    <?
                    switch ($arResult['ITEM']['RESOURCE_TYPE']) {
                        case 'LINK':
                            ?>
                            <tr>
                                <th>Источник данных:</th>
                                <td>Ссылка на файл XML</td>
                            </tr>
                            <tr>
                                <th>Ссылка на файл:</th>
                                <td>
                                    <?=$arResult['ITEM']['FILE_LINK']?>
                                </td>
                            </tr>
                            <tr>
                                <th>Нужна авторизация:</th>
                                <td><?=$arResult['ITEM']['HTTP_AUTH'] == 'Y' ? 'Да' : 'Нет'?></td>
                            </tr>
                            <?
                            if ($arResult['ITEM']['HTTP_AUTH'] == 'Y'):?>
                                <tr>
                                    <th>Логин:</th>
                                    <td><?=$arResult['ITEM']['HTTP_AUTH_LOGIN']?></td>
                                </tr>
                                <tr>
                                    <th>Пароль:</th>
                                    <td><?=substr($arResult['ITEM']['HTTP_AUTH_PASS'], 0, 3).'*****'.substr($arResult['ITEM']['HTTP_AUTH_PASS'], -3)?></td>
                                </tr>
                            <? endif; ?>
                            <?
                            break;

                        case 'FILE':
                            $strFileLink = \Local\Core\Inner\BxModified\CFile::GetPath($arResult['ITEM']['FILE_ID']);
                            ?>
                            <tr>
                                <th>Источник данных:</th>
                                <td>Загруженный файл XML <a href="<?=\Bitrix\Main\Application::getInstance()
                                        ->getContext()
                                        ->getRequest()
                                        ->getRequestedPageDirectory()?>/?getRobofeedXml=<?=urlencode($strFileLink)?>" class="text-warning" target="_blank" title="Скачать"><i class="zmdi zmdi-cloud-download zmdi-hc-fw"></i></a></td>
                            </tr>
                            <?
                            break;
                    }
                    ?>

                    <tr>
                        <th>Дата последнего импорта:</th>
                        <td>
                            <?=( ( $arResult['ITEM']['DATE_LAST_IMPORT'] instanceof \Bitrix\Main\Type\DateTime ) ? $arResult['ITEM']['DATE_LAST_IMPORT']->format('Y-m-d H:i') : 'Импорт не проводился')?>
                        </td>
                    </tr>
                    <tr>
                        <th>Дата последнего <span class="text-warning">успешного</span> импорта:</th>
                        <td>
                            <?=( ( $arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT'] instanceof \Bitrix\Main\Type\DateTime ) ? $arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT']->format('Y-m-d H:i') : 'Успешного импорта не было')?>
                        </td>
                    </tr>

                    <?
                    switch ($arResult['ITEM']['LAST_IMPORT_RESULT']) {
                        case 'SU':
                            ?>
                            <tr>
                                <th>Текущее состояние импорта:</th>
                                <td>
                                    <span class="badge badge-success">Импорт успешен</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Кол-во товаров в Robofeed XML:</th>
                                <td>
                                    <?=number_format($arResult['ITEM']['PRODUCT_TOTAL_COUNT'], 0, '.',' ')?>
                                </td>
                            </tr>
                            <tr>
                                <th>Кол-во импортированных товаров:</th>
                                <td>
                                    <?=number_format($arResult['ITEM']['PRODUCT_SUCCESS_IMPORT'], 0, '.', ' ')?>
                                </td>
                            </tr>
                            <?
                            break;
                        case 'ER':
                            ?>
                            <tr>
                                <th>Текущее состояние импорта:</th>
                                <td>
                                    <span class="badge badge-danger">Ошибка импорта</span>
                                </td>
                            </tr>
                            <?
                            if (!is_null($arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT'])):?>
                                <tr>
                                    <th colspan="2">
                                        На текущий момент не было ни одного успешного импорта. Магазин не может использовать торговые площадки.
                                    </th>
                                </tr>
                            <? endif; ?>

                            <?
                            break;
                        default:
                            ?>
                            <tr>
                                <th>Текущее состояние импорта:</th>
                                <td>
                                    <span class="badge badge-light">Импорт не проводился</span>
                                </td>
                            </tr>
                            <?
                            break;
                    }
                    ?>
                    </tbody>
                </table>

                <?
                if(
                    $arResult['ITEM']['LAST_IMPORT_RESULT'] == 'ER'
                    && !is_null($arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT'])
                )
                {
                    ?>
                    <br>
                    <h4 class="card-title">В работе с торговыми площадками используюся данные от последнего успешного импорта.</h4>
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th class="w-50">Кол-во товаров в Robofeed XML:</th>
                                <td>
                                    <?=number_format($arResult['ITEM']['PRODUCT_TOTAL_COUNT'], 0, '.', ' ')?>
                                </td>
                            </tr>
                            <tr>
                                <th>Кол-во импортированных товаров:</th>
                                <td>
                                    <?=number_format($arResult['ITEM']['PRODUCT_SUCCESS_IMPORT'], 0, '.', ' ')?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?
                }
                ?>

                <? if ($arResult['ITEM']['BEHAVIOR_IMPORT_ERROR'] == \Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID): ?>
                    <div class="alert alert-warning" role="alert">
                        Текущий поведение импорта при ошибке выбрано как
                        "<?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[$arResult['ITEM']['BEHAVIOR_IMPORT_ERROR']]?>".<br />
                        Мы считаем, что появившиеся ошибки в Robofeed XML говорят о нарушении в логике работы формирования Robofeed XML со стороны Вашего сайта, которые могут понести за собой финансовые
                        потери, поэтому настоятельно рекомендуем использовать
                        <b>"<?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_STOP_IMPORT]?>"</b>. К тому же
                        валидные данные могут быть не полными, что повлияет на дальнейшее генерирование файлов
                        на их основании.
                    </div>
                <? endif; ?>

            </div>
        </div>

        <? if (!empty($arResult['LOG'])): ?>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Импорт товаров из Robofeed XML</h4>

                    <div class="flot-chart flot-line"></div>
                    <div class="flot-chart-legends flot-chart-legends--line"></div>

                    <div class="text-center mt-4">
                        <a href="javascript:void(0)" class="btn btn-outline-secondary" data-toggle="collapse" data-target="#collapseShowHistory">Посмотреть подробную историю</a>
                    </div>
                </div>

                <script type="text/javascript">
                    <?
                    $arChartData = [];
                    $arDates = [];
                    $i = 0;
                    foreach ($arResult['LOG'] as $arLog) {

                        switch ( $arLog['IMPORT_RESULT'] )
                        {
                            case 'SU':
                                $arChartData[0][] = [$i, $arLog['PRODUCT_TOTAL_COUNT']];
                                $arChartData[1][] = [$i, $arLog['PRODUCT_SUCCESS_IMPORT']];
                                $arChartData[2][] = [$i, 2];
                                break;
                            case 'NU':
                                $arChartData[0][] = [];
                                $arChartData[1][] = [];
                                $arChartData[2][] = [$i, 1];
                                break;
                            case 'ER':
                                $arChartData[0][] = [$i, $arLog['PRODUCT_TOTAL_COUNT']];
                                $arChartData[1][] = [$i, $arLog['PRODUCT_SUCCESS_IMPORT']];
                                $arChartData[2][] = [$i, 0];
                                break;
                        }
                        $arDates[$i] = [$i, $arLog['DATE_CREATE']->format('d, H:i')];

                        ++$i;
                    }
                    ?>

                    // Chart Data
                    var lineChartData = [
                        {
                            label: 'Товаров в Robofeed XML',
                            data: JSON.parse('<?=json_encode($arChartData[0])?>'),
                            color: 'rgb(241,186,46, 0.8)',
                            lines: {
                                show: true,
                                lineWidth: 2,
                                fill: 1,
                                fillColor: {
                                    colors: ['rgb(241,186,46, 0.1)', 'rgb(241,186,46, 0.2)']
                                }
                            },
                        },
                        {
                            label: 'Товаров импортировано',
                            data: JSON.parse('<?=json_encode($arChartData[1])?>'),
                            color: 'rgb(40,167,69, 0.8)',
                            lines: {
                                show: true,
                                lineWidth: 2,
                                fill: 1,
                                fillColor: {
                                    colors: ['rgb(40,167,69, 0.1)', 'rgb(40,167,69, 0.2)']
                                }
                            },
                        },
                        {
                            label: 'Результат импорта',
                            data: JSON.parse('<?=json_encode($arChartData[2])?>'),
                            color: 'rgba(255,255,255,0.8)',
                            lines: {
                                show: true,
                                lineWidth: 2,
                                fill: 1,
                                fillColor: {
                                    colors: ['rgba(255,255,255,0.1)', 'rgba(255,255,255,0.2)']
                                }
                            },
                            yaxis: 2
                        }
                    ];

                    // Chart Options
                    var lineChartOptions = {
                        series: {
                            lines: {
                                show: true,
                                barWidth: 0.05,
                                fill: 0
                            },
                            points: {
                                show: true
                            }
                        },
                        shadowSize: 0.1,
                        grid : {
                            borderWidth: 1,
                            borderColor: 'rgba(255,255,255,0.1)',
                            show : true,
                            hoverable : true,
                            clickable : true
                        },
                        yaxis: {
                            tickColor: 'rgba(255,255,255,0.1)',
                            tickDecimals: 0,
                            font: {
                                lineHeight: 13,
                                style: 'normal',
                                color: 'rgba(255,255,255,0.75)',
                                size: 11
                            },
                            shadowSize: 0
                        },
                        yaxes: [
                            {
                                position: 'left',
                                min: 0
                            },
                            {
                                position: 'right',
                                ticks: [[0, "Ошибка"], [1, "XML не изменялся"], [2, "Успех"]],
                                min: 0
                            }
                        ],
                        xaxis: {
                            ticks: JSON.parse('<?=json_encode($arDates)?>'),
                            tickColor: 'rgba(255,255,255,0.1)',
                            tickDecimals: 0,
                            font: {
                                lineHeight: 13,
                                style: 'normal',
                                color: 'rgba(255,255,255,0.75)',
                                size: 11
                            },
                            shadowSize: 0
                        },
                        legend:{
                            container: '.flot-chart-legends--line',
                            backgroundOpacity: 0.5,
                            noColumns: 0,
                            lineWidth: 0,
                            labelBoxBorderColor: 'rgba(255,255,255,0)'
                        }
                    };

                    if ($('.flot-line')[0]) {
                        $.plot($('.flot-line'), lineChartData, lineChartOptions);
                    }
                </script>

                <div class="collapse" id="collapseShowHistory">
                    <div class="listview listview--bordered">
                        <?
                        $arResult['LOG'] = array_reverse($arResult['LOG']);
                        foreach ($arResult['LOG'] as $arLog):?>
                            <div class="listview__item d-block">
                                <div class="widget-past-days__info d-block">
                                    <h5><?=\FormatDate('M, d H:i', $arLog['DATE_CREATE']->getTimestamp())?></h5>
                                    <p class="md-0">
                                        <small>
                                            <?
                                            switch ( $arLog['IMPORT_RESULT'] )
                                            {
                                                case 'SU':
                                                    ?>
                                                    <span class="badge badge-success"><?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('IMPORT_RESULT')[$arLog['IMPORT_RESULT']]?></span><br/>
                                                    <b>Кол-во товаров в Robofeed XML:</b> <?=number_format($arLog['PRODUCT_TOTAL_COUNT'], 0, '.', ' ')?><br/>
                                                    <b>Кол-во импортированных товаров:</b> <?=number_format($arLog['PRODUCT_SUCCESS_IMPORT'], 0, '.', ' ')?>
                                                    <?
                                                    break;
                                                case 'NU':
                                                    ?>
                                                    <span class="badge badge-warning"><?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('IMPORT_RESULT')[$arLog['IMPORT_RESULT']]?></span>
                                                    <?
                                                    break;
                                                case 'ER':
                                                    ?>
                                                    <span class="badge badge-danger"><?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('IMPORT_RESULT')[$arLog['IMPORT_RESULT']]?></span>
                                                    <a href="javascript:void(0)" class="btn btn-outline-secondary btn-sm" data-toggle="collapse" data-target="#collapseLog<?=$arLog['ID']?>">Посмотреть отчет об ошибке</a>
                                                    <div class="collapse" id="collapseLog<?=$arLog['ID']?>">
                                                        <div class="card card-body mb-0">
                                                            <h4 class="card-title mb-2">Настройки на момент ошибки</h4>
                                                            <table class="table table-striped">
                                                                <tbody>
                                                                <tr>
                                                                    <th>
                                                                        Тип обработки ошибок:
                                                                    </th>
                                                                    <td>
                                                                        <?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[$arLog['BEHAVIOR_IMPORT_ERROR']]?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>
                                                                        Информировать о не изменившемся Robofeed XML?:
                                                                    </th>
                                                                    <td>
                                                                        <?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('ALERT_IF_XML_NOT_MODIFIED')[$arLog['ALERT_IF_XML_NOT_MODIFIED']]?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>
                                                                        Версия Robofeed XML:
                                                                    </th>
                                                                    <td>
                                                                        <?=($arLog['ROBOFEED_VERSION'] > 0) ? $arLog['ROBOFEED_VERSION'] : 'Не удалось определить'?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>
                                                                        Время в Robofeed XML:
                                                                    </th>
                                                                    <td>
                                                                        <?=( ($arLog['ROBOFEED_DATE'] instanceof \Bitrix\Main\Type\DateTime) ? $arLog['ROBOFEED_DATE']->format('Y-m-d H:i') : 'Не удалось определить' )?>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                            <h4 class="card-title mb-2">Описание ошибки</h4>
                                                            <?=$arLog['ERROR_TEXT']?>
                                                        </div>
                                                    </div>
                                                    <?
                                                    break;
                                            }
                                            ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        <?endforeach;?>
                    </div>
                </div>
            </div>
        <?endif;?>

    </div>

    <div class="col-lg-6">

        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Тариф</h4>
                <div class="actions">
                    <a href="#" class="actions__item zmdi zmdi-edit zmdi-hc-fw" title="Сменить тариф"></a>
                </div>

                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th class="w-50">Тариф:</th>
                        <td>
                            <?=$arResult['TARIFF']['CURRENT']['NAME']?>
                        </td>
                    </tr>
                    <tr>
                        <th>Максимум товаров:</th>
                        <td>
                            <?=number_format($arResult['TARIFF']['CURRENT']['LIMIT_IMPORT_PRODUCTS'], 0, '.', ' ')?>
                        </td>
                    </tr>
                    <tr>
                        <th>Стоимость за площадку в месяц:</th>
                        <td>
                            <?=number_format($arResult['TARIFF']['CURRENT']['PRICE_PER_TRADING_PLATFORM'], 0, '.', ' ')?> руб.
                        </td>
                    </tr>
                    <tr>
                        <th>Используется с:</th>
                        <td>
                            <?=( ( $arResult['TARIFF']['CHANGED_DATE'] instanceof \Bitrix\Main\Type\DateTime ) ? $arResult['TARIFF']['CHANGED_DATE']->format('Y-m-d H:i') : '-' )?>
                        </td>
                    </tr>
                    <? if ($arResult['TARIFF']['CURRENT']['DATE_ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime): ?>
                        <tr>
                            <th>Тариф действует до:</th>
                            <td>
                                <?=$arResult['TARIFF']['CURRENT']['DATE_ACTIVE_TO']->format('Y-m-d H:i')?>
                            </td>
                        </tr>
                        <tr>
                            <th>Будет переключен на тариф:</th>
                            <td>
                                <?=$arResult['TARIFF']['NEXT']['NAME']?>
                            </td>
                        </tr>
                    <? endif; ?>
                    </tbody>
                </table>

            </div>

            <? if (!empty($arResult['TARIFF']['RECOMMEND_TARIFF'])): ?>
                <div class="card-header bg-secondary">
                    <h4 class="card-title text-dark">Рекомендуемый тариф</h4>
                </div>
                <div class="card-body">
                    <h6 class="card-subtitle">Отталкиваясь от данных Robofeed XML мы подобрали для Вас оптимальный тариф.</h6>

                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th class="w-50">Тариф:</th>
                            <td>
                                <?=$arResult['TARIFF']['RECOMMEND_TARIFF']['NAME']?>
                            </td>
                        </tr>
                        <tr>
                            <th>Максимум товаров:</th>
                            <td>
                                <?=number_format($arResult['TARIFF']['RECOMMEND_TARIFF']['LIMIT_IMPORT_PRODUCTS'], 0, '.', ' ')?>
                            </td>
                        </tr>
                        <tr>
                            <th>Стоимость за площадку в месяц:</th>
                            <td>
                                <?=number_format($arResult['TARIFF']['RECOMMEND_TARIFF']['PRICE_PER_TRADING_PLATFORM'], 0, '.', ' ')?> руб.
                            </td>
                        </tr>
                        <? if ($arResult['TARIFF']['RECOMMEND_TARIFF']['DATE_ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime): ?>

                            <tr>
                                <th>Тариф действует до:</th>
                                <td>
                                    <?=$arResult['TARIFF']['RECOMMEND_TARIFF']['DATE_ACTIVE_TO']->format('Y-m-d H:i')?>
                                </td>
                            </tr>
                            <tr>
                                <th>Будет переключен на тариф:</th>
                                <td>
                                    <?=$arResult['TARIFF']['RECOMMEND_TARIFF']['NEXT']['NAME']?>
                                </td>
                            </tr>
                        <? endif; ?>
                        </tbody>
                    </table>

                    <?
                    $strDirection = ( $arResult['TARIFF']['RECOMMEND_TARIFF']['PRICE_PER_TRADING_PLATFORM'] > $arResult['TARIFF']['CURRENT']['PRICE_PER_TRADING_PLATFORM'] ) ? 'up' : 'down';
                    ?>
                    <a href="javascript:void(0)" onclick="PersonalStoreDetailComponent.changeTariff(<?=$arResult['ITEM']['ID']?>, '<?=$arResult['TARIFF']['RECOMMEND_TARIFF']['CODE']?>', '<?=$strDirection?>')" class="btn btn-secondary btn-block">Сменить тариф на рекомендуемый</a>

                </div>

            <?endif;?>

        </div>

        <?
        $GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.tradingplatform.list', '.default', [
            'COMPANY_ID' => $arParams['COMPANY_ID'],
            'STORE_ID' => $arParams['STORE_ID']
        ]);
        ?>

    </div>
</div>
<script type="text/javascript">
    PersonalStoreDetailComponent.setStoreListLink('<?=\Local\Core\Inner\Route::getRouteTo('store', 'list', ['#COMPANY_ID#' => $arResult['ITEM']['COMPANY_ID']])?>');
    PersonalStoreDetailComponent.init();
</script>