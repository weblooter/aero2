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
?>

<div class="col-xs-12">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active">
            <a data-toggle="tab" href="#nav-store" role="tab">Магазин</a>
        </li>
        <li>
            <a data-toggle="tab" href="#nav-tradingplatforms" role="tab">Торговые площадки</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="nav-store" role="tabpanel">

            <br/>
            <div class="row">

                <div class="col-xs-9">
                    <div class="pull-right">
                        <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'edit', ['#COMPANY_ID#' => $arResult['ITEM']['COMPANY_ID'], '#STORE_ID#' => $arResult['ITEM']['ID']])?>" title="Редактировать">
                            <ion-icon name="create" role="img" class="hydrated" aria-label="create"></ion-icon>
                        </a>
                        <a href="javascript:void(0)" onclick="wblDeleteStore(<?=$arResult['ITEM']['ID']?>)" title="Удалить">
                            <ion-icon name="trash" role="img" class="hydrated" aria-label="trash"></ion-icon>
                        </a>
                    </div>

                    Название: <?=$arResult['ITEM']['NAME'];?><br />
                    Сайт: <?=$arResult['ITEM']['DOMAIN'];?><br />
                    <?
                    switch ($arResult['ITEM']['RESOURCE_TYPE']) {
                        case 'LINK':
                            ?>
                            Источник данных: Ссылка на файл XML<br />
                            Для доступа нужен логин и пароль: <?=$arResult['ITEM']['HTTP_AUTH'] == 'Y' ? 'Да' : 'Нет'?><br />
                            <?
                            if ($arResult['ITEM']['HTTP_AUTH'] == 'Y'):?>
                                Логин для авторизации: <?=$arResult['ITEM']['HTTP_AUTH_LOGIN']?><br />
                                Пароль для авторизации: <?=$arResult['ITEM']['HTTP_AUTH_PASS']?><br />
                            <? endif; ?>
                            Ссылка: <a href="<?=$arResult['ITEM']['FILE_LINK']?>" target="_blank"><?=$arResult['ITEM']['FILE_LINK']?></a><br />
                            <?
                            break;

                        case 'FILE':
                            ?>
                            Источник данных: Загруженный файл XML<br />
                            <?
                            $strFileLink = \Local\Core\Inner\BxModified\CFile::GetPath($arResult['ITEM']['FILE_ID']);
                            ?>
                            Файл: <a href="<?=\Bitrix\Main\Application::getInstance()
                            ->getContext()
                            ->getRequest()
                            ->getRequestedPageDirectory()?>/?getRobofeedXml=<?=urlencode($strFileLink)?>" target="_blank" class="btn btn-warning">Скачать загруженный Robofeed XML</a><br />
                            <?
                            break;
                    }
                    ?>
                    Дата последнего импорта: <?=(!is_null($arResult['ITEM']['DATE_LAST_IMPORT'])) ? $arResult['ITEM']['DATE_LAST_IMPORT']->format('Y-m-d H:i:s') : 'Импорт не проводился'?><br />
                    Дата последнего успешного
                    импорта: <?=(!is_null($arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT'])) ? $arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT']->format('Y-m-d H:i:s') : 'Импорт не проводился'?><br />
                    Текущее состояние Robofeed XML: <?
                    switch ($arResult['ITEM']['LAST_IMPORT_RESULT']) {
                        case 'SU':
                            ?>
                            <span class="badge badge-success">Импорт успешен</span><br />
                            Общее кол-во товаров в Robofeed XML составляет <?=number_format($arResult['ITEM']['PRODUCT_TOTAL_COUNT'], 0, '.',
                            ' ')?> шт., из них импортировано - <?=number_format($arResult['ITEM']['PRODUCT_SUCCESS_IMPORT'], 0, '.', ' ')?> шт.
                            <?
                            break;
                        case 'ER':
                            ?>
                            <span class="badge badge-danger">Ошибка импорта</span><br />
                            <?
                            if (!is_null($arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT'])):?>
                                <div class="alert alert-warning" role="alert">
                                    В работе с торговыми площадками используюся данные от последнего успешного импорта.<br />
                                    Дата последнего успешного импорта: <?=$arResult['ITEM']['DATE_LAST_SUCCESS_IMPORT']->format('Y-m-d H:i:s')?><br />
                                    Общее кол-во товаров в Robofeed XML составляет <?=number_format($arResult['ITEM']['PRODUCT_TOTAL_COUNT'], 0, '.', ' ')?> шт., из них импортировано
                                    - <?=number_format($arResult['ITEM']['PRODUCT_SUCCESS_IMPORT'], 0, '.', ' ')?> шт.
                                </div>
                            <? else: ?>
                                <div class="alert alert-danger" role="alert">
                                    На текущий момент не было ни одного успешного импорта.
                                </div>
                            <? endif; ?>

                            <?
                            break;
                        default:
                            ?>
                            <span class="badge badge-dark">Импорт не проводился</span>
                            <?
                            break;
                    }
                    ?><br />
                    <br />

                    <? if (!empty($arResult['TARIFF']['RECOMMEND_TARIFF'])): ?>
                        <div class="card">
                            <h5 class="card-header">Рекомендуемый тариф</h5>
                            <div class="card-body">
                                <h5 class="card-title"><?=$arResult['TARIFF']['RECOMMEND_TARIFF']['NAME']?></h5>
                                <p class="card-text">
                                    На данном тарифе действую ограничение в <?=$arResult['TARIFF']['RECOMMEND_TARIFF']['LIMIT_IMPORT_PRODUCTS']?> товаров.<br />
                                    Стоимость тарифа - <?=number_format($arResult['TARIFF']['RECOMMEND_TARIFF']['PRICE_PER_TRADING_PLATFORM'], 0, '.', ' ')?> руб./мес. за ТП.
                                    <? if ($arResult['TARIFF']['RECOMMEND_TARIFF']['DATE_ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime): ?>
                                        <br />Данный тариф активен до <?=$arResult['TARIFF']['RECOMMEND_TARIFF']['DATE_ACTIVE_TO']->format('Y-m-d H:i')?>. Далее он будет автоматически переключен на
                                        <b>"<?=$arResult['TARIFF']['RECOMMEND_TARIFF']['NEXT']['NAME']?>"</b><br />
                                    <? endif; ?>
                                </p>
                                <?
                                $strDirection = ( $arResult['TARIFF']['RECOMMEND_TARIFF']['PRICE_PER_TRADING_PLATFORM'] > $arResult['TARIFF']['CURRENT']['PRICE_PER_TRADING_PLATFORM'] ) ? 'up' : 'down';
                                ?>
                                <a href="javascript:void(0)" onclick="changeTariff(<?=$arResult['ITEM']['ID']?>, '<?=$arResult['TARIFF']['RECOMMEND_TARIFF']['CODE']?>', '<?=$strDirection?>')" class="btn btn-warning">Перейти на
                                    этот тариф</a>
                            </div>
                        </div>
                        <br />
                    <? endif; ?>

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
                <div class="col-xs-3">
                    <div class="card">
                        <div class="card-header">
                            <small>Тариф</small>
                        </div>
                        <div class="card-body">
                            <b class="card-title"><?=$arResult['TARIFF']['CURRENT']['NAME']?></b>
                            <p class="card-text">
                                <small>
                                    На данном тарифе действую ограничение в <?=$arResult['TARIFF']['CURRENT']['LIMIT_IMPORT_PRODUCTS']?> товаров.<br />
                                    Стоимость тарифа - <?=number_format($arResult['TARIFF']['CURRENT']['PRICE_PER_TRADING_PLATFORM'], 0, '.', ' ')?> руб./мес. за ТП.
                                    <? if ($arResult['TARIFF']['CURRENT']['DATE_ACTIVE_TO'] instanceof \Bitrix\Main\Type\DateTime): ?>
                                        <br />Данный тариф активен до <?=$arResult['TARIFF']['CURRENT']['DATE_ACTIVE_TO']->format('Y-m-d H:i')?>. Далее он будет автоматически переключен на
                                        <b>"<?=$arResult['TARIFF']['NEXT']['NAME']?>"</b><br />
                                    <? endif; ?>
                                </small>
                            </p>
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Изменить тариф
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <? $component->printTariffListHtml() ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Действует с <?=( ( $arResult['TARIFF']['CHANGED_DATE'] instanceof \Bitrix\Main\Type\DateTime ) ? $arResult['TARIFF']['CHANGED_DATE']->format('Y-m-d H:i') : '-' )?></small>
                        </div>
                    </div>
                </div>
                <? if (!empty($arResult['LOG'])): ?>
                    <div class="col-xs-12">
                        <?
                        $arErrorsLog = [];
                        $arChartData = [
                            ['Дата', 'Всего товаров', 'Импортировано товаров', 'Результат импорта']
                        ];
                        foreach ($arResult['LOG'] as $arLog) {
                            $arChartData[] = [
                                date('m-d H:i', $arLog['DATE_CREATE']->getTimestamp()),
                                $arLog['PRODUCT_TOTAL_COUNT'],
                                $arLog['PRODUCT_SUCCESS_IMPORT'],
                                \Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('IMPORT_RESULT')[$arLog['IMPORT_RESULT']]
                            ];

                            if (
                                $arLog['IMPORT_RESULT'] == 'ER'
                                || ($arLog['IMPORT_RESULT'] == 'NU' && $arLog['ALERT_IF_XML_NOT_MODIFIED'] == 'Y')
                            ) {
                                $arErrorsLog[] = $arLog;
                            }
                        }
                        ?>
                        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                        <script type="text/javascript">
                            google.charts.load('current', {'packages': ['bar']});
                            google.charts.setOnLoadCallback(drawChart);

                            function drawChart() {
                                var data = google.visualization.arrayToDataTable(JSON.parse('<?=json_encode($arChartData, JSON_UNESCAPED_UNICODE)?>'));

                                var options = {
                                    title: 'Отчет по импорту товаров из Robofeed XML',
                                    legend: {position: 'none'},
                                    vAxis: {format: '#,###'},
                                    colors: ['#ff4949', 'rgb(255, 179, 0)', 'rgb(34, 34, 34)']
                                };

                                var chart = new google.charts.Bar(document.getElementById('chart_div'));
                                chart.draw(data, google.charts.Bar.convertOptions(options));
                            }
                        </script>
                        <div id="chart_div" style="width: 100%; height: 500px;"></div>
                        <? if (!empty($arErrorsLog)): ?>
                            <? $arErrorsLog = array_reverse($arErrorsLog) ?>
                            <br />
                            <br />
                            <h4>Последние ошибки:</h4>
                            <? foreach ($arErrorsLog as $arLog): ?>
                                <details>
                                    <summary><?=date('Y-m-d H:i', $arLog['DATE_CREATE']->getTimestamp())?></summary>
                                    <div>
                                        Тип обработки ошибок: <?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[$arLog['BEHAVIOR_IMPORT_ERROR']]?><br />
                                        Информировать о не изменившемся Robofeed
                                        XML?: <?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('ALERT_IF_XML_NOT_MODIFIED')[$arLog['ALERT_IF_XML_NOT_MODIFIED']]?><br />
                                        Версия Robofeed XML: <?=($arLog['ROBOFEED_VERSION'] > 0) ? $arLog['ROBOFEED_VERSION'] : 'Не удалось определить'?><br />
                                        Время в Robofeed XML: <?=($arLog['ROBOFEED_DATE'] instanceof \Bitrix\Main\Type\DateTime) ? date('Y-m-d H:i',
                                            $arLog['ROBOFEED_DATE']->getTimestamp()) : 'Не удалось определить'?><br />
                                        Описание ошибки:<br />
                                        <?=$arLog['ERROR_TEXT']?>
                                    </div>
                                </details>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                <? endif; ?>

            </div>

        </div>
        <div class="tab-pane" id="nav-tradingplatforms" role="tabpanel">

            <br/>
            <?
            $GLOBALS['APPLICATION']->IncludeComponent('local.core:personal.tradingplatform.list', '.default', [
                'COMPANY_ID' => $arParams['COMPANY_ID'],
                'STORE_ID' => $arParams['STORE_ID']
            ]);
            ?>

        </div>
    </div>

</div>


<script type="text/javascript">
    function wblDeleteStore(intId) {
        if (confirm('Удалить?')) {
            axios.post('/ajax/store/delete/' + intId + '/')
                .then(function (response) {
                    if (response.data.result == 'SUCCESS') {
                        alert('OK!');
                    } else {
                        alert(response.data['error_text'])
                    }
                })
        }
    }

    function changeTariff($intStoreId, $strTariffCode, $planDirection) {
        var obTextes = {
            up: 'Выбранный тариф дороже текущего. Мы произведем возврат средств по оплаченным торговым площадкам пропорционально их оставшемуся периоду. После это мы проведем списание средств за каждую активную торговую площадку согласно стоимости выбранного тарифа. Убедитесь, что у Вас хватает средств на счету для оплаты всех активных торговых площадок данного магазина. По торговым площадкам, которые на момент смены тарифного плана были деактивированы, так же произойдет возврат, но активированы они не будут. Сменить тариф?',
            down: 'Выбранный тариф дешевле текущего. Средства, оплаченные за текущие торговые площадки, не будут возвращены. Сменить тариф?'
        };
        if (confirm(obTextes[$planDirection])) {
            axios.post('/ajax/store/change_tariff/' + $intStoreId + '/' + $strTariffCode + '/')
                .then(function (response) {
                    if (response.data.result == 'SUCCESS') {
                        alert('Тариф успешно изменен!');
                        location.href = location.href;
                    } else {
                        alert(response.data['error_text'])
                    }
                })
        }
    }
</script>