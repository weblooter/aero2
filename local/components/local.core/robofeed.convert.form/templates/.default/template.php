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
<div class="card">
    <div class="card-body">

        <h4 class="card-title">Загрузить новый файл</h4>
        <? if ($arResult['ADD_STATUS'] == 'SUCCESS'): ?>
            <div class="alert alert-success" role="alert">
                Ваш добавили Ваш файл в очередь на обработку!<br />
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
                        <label>Исходный формат файла * :</label>
                        <select name="CONVERT[HANDLER]" required class="select2" data-minimum-results-for-search="Infinity">
                            <? foreach ($arResult['HANDLERS'] as $k => $v): ?>
                                <option value="<?=$k?>"><?=$v?></option>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-sm-4">
                        <label>Файл * :</label>
                        <input type="file" name="CONVERT[FILE]" class="file" data-show-preview="false" data-msg-placeholder="Загрузите файл" data-show-cancel="false" data-show-upload="false" required />
                        <small class="form-text text-muted">Ограничение по размеру
                            - <?=$component->intMaxUploadFileSizeMb?> Мб.
                        </small>
                    </div>

                    <div class="col-sm-4">
                        <label>&nbsp;</label><br/>
                        <button type="submit" class="btn btn-warning">Отправить на конвертацию</button>
                    </div>
                </div>

                <script type="text/javascript">
                    LocalCore.initFormComponents();
                </script>
            </form>
        <? endif; ?>
        <br />
        <div class="alert alert-info">
            Стоит понимать, что конвертер пытается создать Robofeed XML на основании других форматов, структура которых отличается от структуры, необходимой Robofeed XML.
            По этой причине рассматривать использование данного функционала для получения конечного полноценного Robofeed XML не стоит.<br /><br>
            Данный функционал создан с целью провести быстрое тестирование функционала и возможностей нашего сервиса или использовать полученный файл на время интеграции с Robofeed.<br /><br>
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('development', 'robofeed')?>" class="btn btn-outline-secondary" target="_blank">Как создать Robofeed XML?</a>
        </div>

    </div>
</div>