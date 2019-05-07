<?
/**
 * @var array                       $arParams
 * @var array                       $arResult
 * @var \PersonalStoreFormComponent $component
 * @var \CBitrixComponentTemplate   $this
 * @var string                      $templateName
 * @var string                      $componentPath
 * @var string                      $templateFolder
 * @global CMain                    $APPLICATION
 */

$funIsRequired = function ($strCode) use ($arResult)
    {
        return $arResult['FIELDS'][$strCode]['IS_REQUIRED'];
    }
?>

<div class="card">
    <div class="card-body">
        <? if ($arResult['STATUS'] == 'SUCCESS_ADD'): ?>
            <div class="alert alert-success" role="alert">
                Магазин успешно добавлен!
            </div>
            <script type="text/javascript">
                setTimeout(function () {
                    location.href = '<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arResult['ADD_ID']])?>';
                }, 1500);
            </script>
        <? else: ?>

        <? if ($arResult['STATUS'] == 'SUCCESS_UPDATE'): ?>
            <div class="alert alert-success" role="alert">
                Магазин успешно изменен!
            </div>
        <? endif; ?>

        <?
        $strRoute = '';
        if (!empty($arParams['STORE_ID'])) {
            $strRoute = \Local\Core\Inner\Route::getRouteTo('store', 'edit', ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']]);
        } else {
            $strRoute = \Local\Core\Inner\Route::getRouteTo('store', 'add', ['#COMPANY_ID#' => $arParams['COMPANY_ID']]);
        }
        ?>

            <form method="post" action="<?=$strRoute?>" enctype="multipart/form-data" id="personalstoreformcomponent">
                <?=bitrix_sessid_post();?>
                <? if ($arResult['STATUS'] == 'ERROR'): ?>
                    <div class="alert alert-danger" role="alert">
                        <?=implode('<br/>', $arResult['ERROR_TEXT'])?>
                    </div>
                <? endif; ?>

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Название <?=($funIsRequired('NAME') ? '* ' : '')?>:</label>
                            <input type="text" class="form-control" name="STORE_FIELD[NAME]" <?=($funIsRequired('NAME') ? 'required' : '')?> value="<?=$arResult['FIELDS']['NAME']['VALUE']?>" placeholder="Мой магазин" />
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Ссылка на сайт <?=($funIsRequired('DOMAIN') ? '* ' : '')?>:</label>
                            <input type="text" class="form-control" name="STORE_FIELD[DOMAIN]" <?=($funIsRequired('DOMAIN') ? 'required' : '')?> value="<?=$arResult['FIELDS']['DOMAIN']['VALUE']?>" placeholder="http://example.com" />
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Источник данных <?=($funIsRequired('RESOURCE_TYPE') ? '* ' : '')?>:</label>
                            <select name="STORE_FIELD[RESOURCE_TYPE]" class="select2" required data-minimum-results-for-search="Infinity" data-source-value onchange="PersonalStoreFormComponent.changeSource();">
                                <? foreach (\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('RESOURCE_TYPE') as $v => $t): ?>
                                    <option value="<?=$v?>" <?=$arResult['FIELDS']['RESOURCE_TYPE']['VALUE'] == $v ? 'selected' : ''?> ><?=$t?></option>
                                <? endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div data-source="LINK" class="d-none">

                    <h4 class="card-title">Получение данных по ссылке</h4>
                    <div class="form-group">
                        <label>Ссылка на файл XML * :</label>
                        <input type="text" class="form-control" name="STORE_FIELD[FILE_LINK]" value="<?=$arResult['FIELDS']['FILE_LINK']['VALUE']?>" />
                        <small class="form-text text-muted">Ссылка должна вести на Robofeed XML. Так же стоит
                            учитывать, что мы не принимаем файлы, генерируемые "на лету", т.к. время ожидания и скачивания файла
                            ограничено!<br />
                            Ограничение по размеру - <?=$component->intMaxDownloadXMLFileSizeMb?> Мб.<br />
                            <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Что такое Robofeed XML?</a>
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="STORE_FIELD[HTTP_AUTH]" value="Y" <?=$arResult['FIELDS']['HTTP_AUTH']['VALUE']
                                                                                                                           == 'Y' ? 'checked' : ''?> data-need-access-value onchange="PersonalStoreFormComponent.changeLinkAccess()" />
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Для доступа нужен логин и пароль</span>
                        </label>
                        <small class="form-text text-muted">
                            Подразумевается, что для получения файла необходима HTTP (Basic) авторизация.
                        </small>
                    </div>
                    <div class="d-none" data-need-access>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Логин для авторизации</label>
                                    <input type="text" class="form-control" name="STORE_FIELD[HTTP_AUTH_LOGIN]" value="<?=$arResult['FIELDS']['HTTP_AUTH_LOGIN']['VALUE']?>" />
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label>Пароль для авторизации</label>
                                    <input type="text" class="form-control" name="STORE_FIELD[HTTP_AUTH_PASS]" value="<?=$arResult['FIELDS']['HTTP_AUTH_PASS']['VALUE']?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div data-source="FILE" class="d-none">
                    <h4 class="card-title">Получение данных из файла</h4>
                    <div class="form-group">
                        <label>Файл Robofeed XML * :</label>
                        <label class="dropzone dz-clickable d-block">
                            <div class="dz-default dz-message">
                                <span data-file-title>Выберите файл</span>
                            </div>
                            <input type="file" name="STORE_FIELD[FILE]" class="d-none" onchange="PersonalStoreFormComponent.changeFile();" data-max-size="<?=$component->intMaxUploadXMLFileSizeMb?>" />
                        </label>
                        <input type="hidden" name="STORE_FIELD[OLD_FILE]" value="<?=$arResult['FIELDS']['FILE_ID']['VALUE']?>" />

                        <small class="form-text text-muted">Может быть загружен только Robofeed XML. Ограничение по размеру
                            - <?=$component->intMaxUploadXMLFileSizeMb?> Мб.<br />
                            <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">Что такое Robofeed XML?</a>
                        </small>
                    </div>

                </div>

                <h4 class="card-title">Дополнительне настройки</h4>

                <div class="form-group">
                    <label>
                        Поведение импорта при ошибке <?=($funIsRequired('BEHAVIOR_IMPORT_ERROR') ? '* ' : '')?>: <a href="javascript:void(0)" data-toggle="collapse" data-target="#collapseBEHAVIOR_IMPORT_ERROR" class="lead text-secondary"><i class="zmdi zmdi-help-outline zmdi-hc-fw"></i></a>
                    </label>
                    <select name="STORE_FIELD[BEHAVIOR_IMPORT_ERROR]" class="select2" required required data-minimum-results-for-search="Infinity">
                        <? foreach (\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR') as $val => $text): ?>
                            <option value="<?=$val?>" <?=$arResult['FIELDS']['BEHAVIOR_IMPORT_ERROR']['VALUE'] == $val ? 'selected' : ''?> ><?=$text?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <div class="collapse" id="collapseBEHAVIOR_IMPORT_ERROR">
                    <div class="alert alert-info">
                        Во время импорта Robofeed XML автоматически проходит предварительную проверку.<br />
                        Данным параметром необходимо задать сценарий действий, в случае выявления ошибки при валидации Robofeed XML.<br />
                        <b><?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_STOP_IMPORT]?></b> - прекращает
                        работу
                        импорта, в работе остаются старые данные.<br />
                        <b><?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID]?></b> -
                        продолжает импорт, но импортирует только валидные данные.<br />
                        Мы считаем, что появившиеся ошибки в Robofeed XML говорят о нарушении в логике работы формирования Robofeed XML со стороны Вашего сайта, которые могут понести за собой финансовые потери, поэтому рекомендуем использовать
                        "<b><?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_STOP_IMPORT]?></b>". К тому же
                        валидные данные могут быть неполными, что повлияет на дальнейшее генерирование файлов на их
                        основании.<br />
                        Вне зависимости от выбранного поведения мы проинформируем Вас о проблемах, если таковые появятся.<br/>
                        <br/>
                        <b>Обращаем Ваше внимание</b>, что если Вы меняете настройку с <b>"<?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_STOP_IMPORT]?>"</b> на <b>"<?=\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('BEHAVIOR_IMPORT_ERROR')[\Local\Core\Model\Data\StoreTable::BEHAVIOR_IMPORT_ERROR_IMPORT_ONLY_VALID]?>"</b> и у Вас уже была неудачная попытка импорта, то необходимо убедиться, чтобы дата создания в грядущем Robofeed XML ( robofeed->lastModified ) отличалась от даты создания уже импортированного файла. В противном случае импорт будет воспринят воспринят как новый и в результате Вы получите статус <b>"Robofeed XML не изменялся"</b>.
                    </div>
                </div>


                <div class="form-group">
                    <label>
                        Информировать о не изменившемся Robofeed XML? <?=($funIsRequired('ALERT_IF_XML_NOT_MODIFIED') ? '* ' : '')?>: <a href="javascript:void(0)" data-toggle="collapse" data-target="#collapseALERT_IF_XML_NOT_MODIFIED" class="lead text-secondary"><i class="zmdi zmdi-help-outline zmdi-hc-fw"></i></a>
                    </label>
                    <select name="STORE_FIELD[ALERT_IF_XML_NOT_MODIFIED]" class="select2" required required data-minimum-results-for-search="Infinity">
                        <? foreach (\Local\Core\Model\Data\StoreTable::getEnumFieldHtmlValues('ALERT_IF_XML_NOT_MODIFIED') as $val => $text): ?>
                            <option value="<?=$val?>" <?=$arResult['FIELDS']['ALERT_IF_XML_NOT_MODIFIED']['VALUE'] == $val ? 'selected' : ''?> ><?=$text?></option>
                        <? endforeach; ?>
                    </select>
                </div>

                <div class="collapse" id="collapseALERT_IF_XML_NOT_MODIFIED">
                    <div class="alert alert-info">
                        Мы стараемся импортировать Ваш Robofeed XML как можно чаще, чтобы передавать на торговые площадки как можно более актуальную информацию. На текущий момент это ориентировочно
                        каждые 4
                        часа.<br />
                        Но некоторые магазины обновляют Robofeed XML раз в день или еще реже.<br />
                        Нам необходимо понимать как относиться к ситуации, когда Robofeed XML не изменился - является ли это ошибкой, означающей проблемы на Вашем магазине, или же это нормальная ситуация.<br />
                        При выборе <b>"Да"</b> Вы будете немедленно проинформированы о данной ситуации.<br />
                        При выборе <b>"Нет"</b> мы будем воспринимать такую ситуацию, как допустимую и Вы не будете проинформированы.<br />
                        <b>"Да"</b> следует выбрать, если выбран источник данных "Ссылка на файл" и Вы обновляете Robofeed XML каждые 3 часа или чаще. В остальных случаях рекомендуем выставить
                        <b>"Нет"</b>.
                    </div>
                </div>

                <? if ($arResult['STATUS'] == 'ERROR'): ?>
                    <div class="alert alert-danger" role="alert">
                        <?=implode('<br/>', $arResult['ERROR_TEXT'])?>
                    </div>
                <? endif; ?>


                <? if ($arResult['STATUS'] == 'SUCCESS_UPDATE'): ?>
                    <div class="alert alert-success" role="alert">
                        Магазин успешно изменен!
                    </div>
                <? endif; ?>

                <? if ($arParams['STORE_ID'] > 0): ?>
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'detail',
                        ['#COMPANY_ID#' => $arParams['COMPANY_ID'], '#STORE_ID#' => $arParams['STORE_ID']])?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться в магазин</a>
                    <button class="btn btn-warning">
                        Изменить магазин
                    </button>
                <? else: ?>
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'list', ['#COMPANY_ID#' => $arParams['COMPANY_ID']])?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться
                        к магазинам</a>
                    <button class="btn btn-warning">
                        Создать магазин
                    </button>
                <? endif; ?>

                <script type="text/javascript">
                    LocalCore.initFormComponents();
                    PersonalStoreFormComponent.init();
                </script>

            </form>

        <? endif; ?>
    </div>
</div>


