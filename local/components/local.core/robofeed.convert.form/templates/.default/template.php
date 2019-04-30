<?
/**
 * @global CMain                      $APPLICATION
 * @var array                         $arParams
 * @var array                         $arResult
 * @var \RobofeedConvertFormComponent $component
 * @var CBitrixComponentTemplate      $this
 * @var string                        $templateName
 * @var string                        $componentPath
 * @var string                        $templateFolder
 */
?>

<? if ($arResult['ADD_STATUS'] == 'SUCCESS'): ?>
    <div class="alert alert-success" role="alert">
        Ваш файл находится в очереди на обработку!<br />
        Мы сообщим Вам по электронной почте о результате.
    </div>

<? else: ?>
    <form action="<?=\Bitrix\Main\Application::getInstance()
        ->getContext()
        ->getRequest()
        ->getRequestedPageDirectory()?>/" method="post" enctype="multipart/form-data">
        <?=bitrix_sessid_post();?>
        <? if ($arResult['ADD_STATUS'] == 'ERROR'): ?>
            <div class="alert alert-danger" role="alert">
                <?=implode('<br/>', $arResult['ERROR_TEXT'])?>
            </div>
        <? endif; ?>

        <div class="row">
            <div class="form-group col-sm-4">
                <label>Исходный формат файла *</label>
                <div class="selectwrapper">
                <select name="CONVERT[HANDLER]" required>
                    <? foreach ($arResult['HANDLERS'] as $k => $v): ?>
                        <option value="<?=$k?>"><?=$v?></option>
                    <? endforeach; ?>
                </select>
                </div>
            </div>

            <div class="form-group col-sm-4">
                <label>Файл *</label>
                <div class="custom-file">
                    <input type="file" name="CONVERT[FILE]" class="custom-file-input" id="convertfile" required>
                </div>
                <small class="form-text text-muted">Ограничение по размеру
                    - <?=$component->intMaxUploadFileSizeMb?> Мб.
                </small>
            </div>

            <div class="col-sm-4">
                <label for="">&nbsp;</label>
                <button type="submit" class="button orange">Отправить на конвертацию</button>
            </div>
        </div>

        <? if ($arResult['ADD_STATUS'] == 'ERROR'): ?>
            <div class="alert alert-danger" role="alert">
                <?=implode('<br/>', $arResult['ERROR_TEXT'])?>
            </div>
        <? endif; ?>
    </form>
<? endif; ?>
<br />
<div class="alert alert-warning" role="alert">
    Стоит понимать, что конвертер пытается создать Robofeed XML на основании других форматов, структура которых отличается от структуры, необходимой Robofeed XML.
    По этой причине рассматривать использование данного функционала для получения конечного полноценного Robofeed XML не стоит.<br /><br>
    Данный функционал создан с целью провести быстрое тестирование функционала и возможностей нашего сервиса или использовать полученный файл на время интеграции с Robofeed.<br /><br>
    Изучите, <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">как создать Robofeed XML</a>.
</div>