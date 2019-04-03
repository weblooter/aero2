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
        Мы поставили Ваш файл в очередь на обработку!<br />
        Мы сообщим Вам по элетронной почте о результате.
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


        <div class="form-group">
            <label>Исходный формат файла *</label>
            <select name="CONVERT[HANDLER]" class="form-control" required>
                <? foreach ($arResult['HANDLERS'] as $k => $v): ?>
                    <option value="<?=$k?>"><?=$v?></option>
                <? endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Файл *</label>
            <div class="custom-file">
                <input type="file" name="CONVERT[FILE]" class="custom-file-input" id="convertfile" required>
                <label class="custom-file-label" for="convertfile">Choose file</label>
            </div>
            <small class="form-text text-muted">Ограничение по размеру
                - <?=$component->intMaxUploadFileSizeMb?> Мб.
            </small>
        </div>
        <button type="submit" class="btn btn-warning">Submit</button>

        <? if ($arResult['ADD_STATUS'] == 'ERROR'): ?>
            <div class="alert alert-danger" role="alert">
                <?=implode('<br/>', $arResult['ERROR_TEXT'])?>
            </div>
        <? endif; ?>
    </form>
<? endif; ?>
<br />
<div class="alert alert-warning" role="alert">
    Стоит понимать, что конвертер пытается создать Robofeed XML на основании других форматов, структура которых отличается от структуры, необходимой Robofeed XML.<br />
    По этому причине рассматривать использование данного функционала для получения конечного полноценного Robofeed XML не стоит.<br />
    Данный функционал сделан с целью провести быстрое тестирование функционала и возможностей нашего сервиса или использовать полученный файл на время разработки сборщика Robofeed XML.<br />
    Изучите <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" target="_blank">как сделать Robofeed XML</a>.
</div>