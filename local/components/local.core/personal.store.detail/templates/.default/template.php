<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var \PersonalStoreDetailComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */
?>

<div class="container-fluid">
    <div class="row">

        <div class="col-12">
            <div class="pull-right">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'edit', ['#COMPANY_ID#' => $arResult['ITEM']['COMPANY_ID'], '#STORE_ID#' => $arResult['ITEM']['ID']])?>" title="Редактировать">
                    <ion-icon name="create" role="img" class="hydrated" aria-label="create"></ion-icon>
                </a>
                <a href="#" title="Удалить">
                    <ion-icon name="trash" role="img" class="hydrated" aria-label="trash"></ion-icon>
                </a>
            </div>

            Название: <?=$arResult['ITEM']['NAME'];?><br />
            Сайт: <?=$arResult['ITEM']['DOMAIN'];?><br />
            Активность: <?=$arResult['ITEM']['ACTIVE'] == 'Y' ? 'Да' : 'Нет';?><br />
            <?
            switch( $arResult['ITEM']['RESOURCE_TYPE'] )
            {
                case 'LINK':
                    ?>
                    Источник данных: Ссылка на файл XML<br />
                    Для доступа нужен логин и пароль: <?=$arResult['ITEM']['HTTP_AUTH'] == 'Y' ? 'Да' : 'Нет'?><br />
                    <?
                    if( $arResult['ITEM']['HTTP_AUTH'] == 'Y' ):?>
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
                    Файл: <a href="<?=$strFileLink?>" target="_blank"><?=$strFileLink?></a><br />
                    <?
                    break;
            }
            ?>

            <?
            if( !empty($arResult['LOG']) && is_array($arResult['LOG']) )
            {
                $arLastLog = end($arResult['LOG']);

                switch( $arLastLog['IMPORT_COMPLETED'] )
                {
                    case 'Y':
                        ?>
                        <div class="alert alert-success" role="alert">
                            Статус последнего импорта - импорт успешен.
                        </div>
                        <?
                        if( !empty($arLastLog['ERROR_TEXT']) && $arLastLog['BEHAVIOR_IMPORT_ERROR'] == \Local\Core\Model\Robofeed\ImportLogTable::BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID ):?>
                            <div class="alert alert-danger" role="alert">
                                Во время последнего импорта был выставлен режим обработки ошибок "<?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues(
                                    'BEHAVIOR_IMPORT_ERROR'
                                )[$arLastLog['BEHAVIOR_IMPORT_ERROR']]?>" и вынесено предупреждение.<br />
                                <details>
                                    <summary><u>Читать предупреждение</u></summary>
                                    <div>
                                        <?=$arLastLog['ERROR_TEXT']?>
                                    </div>
                                </details>
                            </div>
                        <?endif; ?>
                        <?
                        break;
                    case 'E':
                        $arLastSuccessProduct = [];
                        if(
                        \Bitrix\Main\Application::getConnection()
                            ->isTableExists(( \Local\Core\Model\Robofeed\StoreProductFactory::factory(1) )->setStoreId($arResult['ITEM']['ID'])::getTableName())
                        )
                        {
                            $arLastSuccessProduct = ( \Local\Core\Model\Robofeed\StoreProductFactory::factory(1) )->setStoreId($arResult['ITEM']['ID'])::getList(
                                [
                                    'order' => ['DATE_CREATE' => 'ASC'],
                                    'limit' => 1,
                                    'offset' => 0,
                                    'select' => ['DATE_CREATE', 'ROBOFEED_VERSION']
                                ]
                            )
                                ->fetch();
                        }
                        ?>
                        <div class="alert alert-danger" role="alert">
                            Статус последнего импорта - во время импорта обнаружена ошибка.<br />
                            <?=$arLastLog['ERROR_TEXT'];?><br/>
                            <?
                            if( !empty($arLastSuccessProduct) ):?>
                                В работе с торговыми площадками учавствуют товары от импорта <?=$arLastSuccessProduct['DATE_CREATE']?>, Robofeed XML версии "<?=$arLastSuccessProduct['ROBOFEED_VERSION']?>"
                            <? else:?>
                                На текущий момент не было ни одного удачного импорта.
                            <?endif; ?>
                        </div>
                        <?
                        break;
                }
            }
            else
            {
                ?>
                <div class="alert alert-dark" role="alert">
                    Статус последнего импорта - импорт еще не проводился.
                </div>
                <?
            }
            ?>

            <? if( $arResult['ITEM']['BEHAVIOR_IMPORT_ERROR'] == \Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID ): ?>
                <div class="alert alert-warning" role="alert">
                    Текущий поведение импорта при ошибке выбрано как "<?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues(
                        'BEHAVIOR_IMPORT_ERROR'
                    )[$arResult['ITEM']['BEHAVIOR_IMPORT_ERROR']]?>".<br />
                    Мы считаем, что появившиеся ошибки в Robofeed XML говорят о нарушении в логике работы формирования Robofeed XML со стороны Вашего сайта, которые могут понести за собой финансовые
                    потери, поэтому настоятельно рекомендуем использовать <b>"<?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues(
                            'BEHAVIOR_IMPORT_ERROR'
                        )[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_STOP_IMPORT]?>"</b>. К тому же валидные данные могут быть не полными, что повлияет на дальнейшее генерирование файлов
                    на их основании.
                </div>
            <? endif; ?>

        </div>
        <? if( !empty($arResult['LOG']) ): ?>
            <div class="col-12">
                <?
                $arErrorsLog = [];
                $arChartData = [
                    ['Дата', 'Всего товаров', 'Валидных товаров', 'Результат импорта']
                ];
                foreach( $arResult['LOG'] as $arLog )
                {
                    $arChartData[] = [
                        date('m-d H:i', $arLog['DATE_CREATE']->getTimestamp()),
                        $arLog['PRODUCT_TOTAL_COUNT'],
                        $arLog['PRODUCT_SUCCESS_IMPORT'],
                        ( $arLog['IMPORT_COMPLETED'] == 'Y' ? 'Успех' : 'Ошибка' )
                    ];

                    if( $arLog['IMPORT_COMPLETED'] == 'E' )
                    {
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
                            legend: {position: 'none'}
                        };

                        var chart = new google.charts.Bar(document.getElementById('chart_div'));
                        chart.draw(data, google.charts.Bar.convertOptions(options));
                    }
                </script>
                <div id="chart_div" style="width: 100%; height: 500px;"></div>
                <? if( !empty($arErrorsLog) ): ?>
                    <br />
                    <br />
                    <h4>Последние ошибки:</h4>
                    <? foreach( $arErrorsLog as $arLog ): ?>
                        <details>
                            <summary><?=date('Y-m-d H:i', $arLog['DATE_CREATE']->getTimestamp())?></summary>
                            <div>
                                Тип обработки ошибок: <?=\Local\Core\Model\Robofeed\ImportLogTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[$arLog['BEHAVIOR_IMPORT_ERROR']]?><br />
                                Версия Robofeed XML: <?=( $arLog['ROBOFEED_VERSION'] > 0 ) ? $arLog['ROBOFEED_VERSION'] : 'Не удалось определить'?><br />
                                Время в Robofeed XML: <?=( $arLog['ROBOFEED_DATE'] instanceof \Bitrix\Main\Type\DateTime ) ? date('Y-m-d H:i', $arLog['ROBOFEED_DATE']->getTimestamp()) : 'Не удалось определить'?><br />
                                Описание ошибки:<br />
                                <?=$arLog['ERROR_TEXT']?>
                            </div>
                        </details>
                    <? endforeach; ?>
                <? endif; ?>
            </div>
        <? endif; ?>

        <div class="col-6">
            <div class="alert alert-primary" role="alert">
                // TODO<br />
                Список выбранных фидов<br />
                <ion-icon name="done-all"></ion-icon>
                Яндекс маркет, Готов к выгрузке
                <hr />
                <ion-icon name="warning"></ion-icon>
                Беру ру, Необходимо проставить соответствия
                <hr />
                <ion-icon name="hourglass"></ion-icon>
                Озон, Проверяется
                <hr />
                <a href="#">Список фидов</a>
            </div>
        </div>

    </div>
</div>
