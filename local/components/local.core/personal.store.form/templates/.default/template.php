<?
/**
 * @global CMain                    $APPLICATION
 * @var array                       $arParams
 * @var array                       $arResult
 * @var \PersonalStoreFormComponent $component
 * @var \CBitrixComponentTemplate   $this
 * @var string                      $templateName
 * @var string                      $componentPath
 * @var string                      $templateFolder
 */

$funIsRequired = function($strCode) use ($arResult)
    {
        return $arResult['FIELDS'][$strCode]['IS_REQUIRED'];
    }
?>

<? if( $arResult['STATUS'] == 'SUCCESS_ADD' ): ?>
    <div class="alert alert-success" role="alert">
        Магазин успешно добавлен!
    </div>
    <script type="text/javascript">
        setTimeout(function () {
            location.href = '<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arResult['ADD_ID']])?>';
        }, 3000);
    </script>
<? else: ?>

    <? if( $arResult['STATUS'] == 'SUCCESS_UPDATE' ): ?>
        <div class="alert alert-success" role="alert">
            Магазин успешно изменен!
        </div>
    <? endif; ?>

    <?
    $strRoute = '';
    if( !empty($arParams['STORE_ID']) )
    {
        $strRoute = \Local\Core\Inner\Route::getRouteTo(
            'store',
            'edit',
            ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']]
        );
    }
    else
    {
        $strRoute = \Local\Core\Inner\Route::getRouteTo(
            'store',
            'add',
            ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
        );
    }
    ?>

    <form method="post" action="<?=$strRoute?>" enctype="multipart/form-data">
        <?=bitrix_sessid_post();?>
        <? if( $arResult['STATUS'] == 'ERROR' ): ?>
            <div class="alert alert-danger" role="alert">
                <?=implode(
                    '<br/>',
                    $arResult['ERROR_TEXT']
                )?>
            </div>
        <? endif; ?>

        <div class="form-group">
            <label>Название <?=( $funIsRequired('NAME') ? '*' : '' )?></label>
            <input type="text" class="form-control" name="STORE_FIELD[NAME]" <?=( $funIsRequired(
                'NAME'
            ) ? 'required' : '' )?> value="<?=$arResult['FIELDS']['NAME']['VALUE']?>" placeholder="Мой магазин" />
        </div>

        <div class="form-group">
            <label>Домен <?=( $funIsRequired('DOMAIN') ? '*' : '' )?></label>
            <input type="text" class="form-control" name="STORE_FIELD[DOMAIN]" <?=( $funIsRequired(
                'DOMAIN'
            ) ? 'required' : '' )?> value="<?=$arResult['FIELDS']['DOMAIN']['VALUE']?>" placeholder="http://example.com" />
            <small class="form-text text-muted">С http://</small>
        </div>
        <div class="form-group">
            <label>Источник данных <?=( $funIsRequired('RESOURCE_TYPE') ? '*' : '' )?></label>
            <select name="STORE_FIELD[RESOURCE_TYPE]" class="custom-select mb-3" required>
                <? foreach( \Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('RESOURCE_TYPE') as $v => $t ): ?>
                    <option value="<?=$v?>" <?=$arResult['FIELDS']['RESOURCE_TYPE']['VALUE'] == $v ? 'selected' : ''?> ><?=$t?></option>
                <? endforeach; ?>
            </select>
        </div>

        <div class="alert alert-dark" role="alert">

            <div class="alert alert-warning" role="alert">
                Если выбран источник "Ссылка на файл"
            </div>
            <div class="form-group">
                <label>Ссылка на файл XML *</label>
                <input type="text" class="form-control" name="STORE_FIELD[FILE_LINK]" value="<?=$arResult['FIELDS']['FILE_LINK']['VALUE']?>" />
                <small class="form-text text-muted">Ссылка должна вести на Robofeed XML. Так же стоит
                    учитывать, что мы не принимаем файлы, генерируемые "на лету", т.к. время ожидания и скачивания файла
                    ограничено!<br />
                    Ограничение по размеру - <?=$component->intMaxDownloadXMLFileSizeMb?> Мб.<br />
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Что такое Robofeed XML?</a>
                </small>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="STORE_FIELD[HTTP_AUTH]" value="Y" <?=$arResult['FIELDS']['HTTP_AUTH']['VALUE']
                                                                                                                   == 'Y' ? 'checked' : ''?> id="customSwitch1" />
                    <label class="custom-control-label" for="customSwitch1">Для доступа нужен логин и пароль</label>
                </div>
            </div>
            <div class="alert alert-warning" role="alert">
                Если выбран "Для доступа нужен логин и пароль"
            </div>
            <div class="form-group">
                <label>Логин для авторизации</label>
                <input type="text" class="form-control" name="STORE_FIELD[HTTP_AUTH_LOGIN]" value="<?=$arResult['FIELDS']['HTTP_AUTH_LOGIN']['VALUE']?>" />
            </div>
            <div class="form-group">
                <label>Пароль для авторизации</label>
                <input type="text" class="form-control" name="STORE_FIELD[HTTP_AUTH_PASS]" value="<?=$arResult['FIELDS']['HTTP_AUTH_PASS']['VALUE']?>" />
            </div>

        </div>


        <div class="alert alert-dark" role="alert">
            <div class="alert alert-warning" role="alert">
                Если выбран источник "Загрузить файл"
            </div>
            <div class="form-group">
                <label>Файл XML *</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="STORE_FIELD[FILE]" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
                    <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                </div>
                <small class="form-text text-muted">Может быть загружен только Robofeed XML. Ограничение по размеру
                    - <?=$component->intMaxUploadXMLFileSizeMb?> Мб.<br />
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Что такое Robofeed XML?</a>
                </small>
                <input type="hidden" name="STORE_FIELD[OLD_FILE]" value="<?=$arResult['FIELDS']['FILE_ID']['VALUE']?>" />
            </div>

        </div>

        <div class="form-group">
            <label>Поведение импорта при ошибке <?=( $funIsRequired('BEHAVIOR_IMPORT_ERROR') ? '*' : '' )?></label>
            <select name="STORE_FIELD[BEHAVIOR_IMPORT_ERROR]" class="custom-select mb-3" required>
                <? foreach( \Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR') as $val => $text ): ?>
                    <option value="<?=$val?>" <?=$arResult['FIELDS']['BEHAVIOR_IMPORT_ERROR']['VALUE'] == $val ? 'selected' : ''?> ><?=$text?></option>
                <? endforeach; ?>
            </select>
        </div>
        <div class="alert alert-warning" role="alert">
            Во время импорта Robofeed XML автоматически проходит предварительную проверку.<br />
            Данным параметром необходимо задать сценарий действий, в случае выявления ошибки при валидации Robofeed XML.<br />
            <b><?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_STOP_IMPORT]?></b> - прекращает работу
            импорта, в работе остаются старые данные.<br />
            <b><?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID]?></b> - продолжает
            импорт, но импортирует только валидные данные.<br />
            Мы считаем, что появившиеся ошибки в Robofeed XML говорят о нарушении в логике работы формирования Robofeed XML со стороны Вашего сайта, которые могут понести за собой финансовые потери,
            поэтому рекомендуем использовать "<b><?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues(
                    'BEHAVIOR_IMPORT_ERROR'
                )[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_STOP_IMPORT]?></b>". К тому же валидные данные могут быть не полными, что повлияет на дальнейшее генерирование файлов на их
            основании.<br />
            Вне зависимости от выбранного поведения мы проинформируем Вас о проблемах, если таковые появятся.
        </div>


        <div class="form-group">
            <label>Воспринимать не обновленный Robofeed XML как ошибку? <?=( $funIsRequired('NOT_UPDATED_XML_IS_ERROR') ? '*' : '' )?></label>
            <select name="STORE_FIELD[NOT_UPDATED_XML_IS_ERROR]" class="custom-select mb-3" required>
                <? foreach( \Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('NOT_UPDATED_XML_IS_ERROR') as $val => $text ): ?>
                    <option value="<?=$val?>" <?=$arResult['FIELDS']['NOT_UPDATED_XML_IS_ERROR']['VALUE'] == $val ? 'selected' : ''?> ><?=$text?></option>
                <? endforeach; ?>
            </select>
        </div>

        <div class="alert alert-warning" role="alert">
            Мы стараемся импортировать Ваш Robofeed XML как можно чаще, что бы передавать на торговые площадки как можно более актуальную информацию. На текущий момент это ориентировочно каждые 4
            часа.<br />
            Но некоторые магазины обновляют Robofeed XML раз в день или еще реже.<br />
            Нам необходимо понимать как относиться к ситуации, когда Robofeed XML не изменился - является ли это ошибкой, означающей проблемы на Вашем магазине, или же это нормальная ситуация.<br />
            При выборе <b>"Да"</b> Вы будете немедлено проинформированны о данной ситуации, а результат импорта будет помечен как "Импорт с ошибкой".<br />
            При выборе <b>"Нет"</b> мы будем воспринимать такую ситуацию, как допустимую, Вы не будете проинформированны, а импорт будет помечен как "Успешный".<br />
            <b>"Да"</b> следует выбрать, если выбран источник данных "Ссылка на файл" и Вы обновляете Robofeed XML каждые 3 часа или чаще. В остальных случаях рекомендуем выставить <b>"Нет"</b>.
        </div>

        <? if( $arResult['STATUS'] == 'ERROR' ): ?>
            <div class="alert alert-danger" role="alert">
                <?=implode(
                    '<br/>',
                    $arResult['ERROR_TEXT']
                )?>
            </div>
        <? endif; ?>


        <? if( $arResult['STATUS'] == 'SUCCESS_UPDATE' ): ?>
            <div class="alert alert-success" role="alert">
                Магазин успешно изменен!
            </div>
        <? endif; ?>

        <? if( $arParams['STORE_ID'] > 0 ): ?>
            <div class="form-group">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo(
                    'store',
                    'detail',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']]
                )?>" class="btn btn-dark">
                    <ion-icon name="arrow-round-back"></ion-icon>
                    Вернуться к магазину</a>
                <button class="btn btn-warning">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    Изменить магазин
                </button>
            </div>
        <? else: ?>
            <div class="form-group">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo(
                    'store',
                    'list',
                    ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
                )?>" class="btn btn-dark">
                    <ion-icon name="arrow-round-back"></ion-icon>
                    Вернуться к магазинам</a>
                <button class="btn btn-warning">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    Создать магазин
                </button>
            </div>
        <? endif; ?>

    </form>

<? endif; ?>

