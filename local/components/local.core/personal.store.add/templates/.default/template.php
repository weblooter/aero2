<?
/**
 * @global CMain                  $APPLICATION
 * @var array                     $arParams
 * @var array                     $arResult
 * @var \PersonalSiteAddComponent $component
 * @var \CBitrixComponentTemplate $this
 * @var string                    $templateName
 * @var string                    $componentPath
 * @var string                    $templateFolder
 */

$funIsRequired = function($strCode) use ($arResult)
    {
        return $arResult['FIELDS'][$strCode]['IS_REQUIRED'];
    }
?>

<? if( $arResult['ADD_STATUS'] == 'SUCCESS' ): ?>
    <div class="alert alert-success" role="alert">
        Магазин успешно добавлен!
    </div>

<? else: ?>

    <form method="post" action="<?=\Local\Core\Inner\Route::getRouteTo(
        'store',
        'add',
        ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
    )?>" enctype="multipart/form-data">
        <?=bitrix_sessid_post();?>
        <? if( $arResult['ADD_STATUS'] == 'ERROR' ): ?>
            <div class="alert alert-danger" role="alert">
                <?=implode(
                    '<br/>',
                    $arResult['ERROR_TEXT']
                )?>
            </div>
        <? endif; ?>

        <div class="form-group">
            <label>Название <?=( $funIsRequired('NAME') ? '*' : '' )?></label>
            <input type="text" class="form-control" name="STORE_FIELD[NAME]" <?=( $funIsRequired('NAME') ? 'required' : '' )?> value="<?=$arResult['FIELDS']['NAME']['VALUE']?>" placeholder="Мой магазин" />
        </div>

        <div class="form-group">
            <label>Домен <?=( $funIsRequired('DOMAIN') ? '*' : '' )?></label>
            <input type="text" class="form-control" name="STORE_FIELD[DOMAIN]" <?=( $funIsRequired('DOMAIN') ? 'required' : '' )?> value="<?=$arResult['FIELDS']['DOMAIN']['VALUE']?>" placeholder="http://example.com" />
            <small class="form-text text-muted">С http://</small>
        </div>

        <div class="form-group">
            <label>Источник данных <?=( $funIsRequired('RESOURCE_TYPE') ? '*' : '' )?></label>
            <select name="STORE_FIELD[RESOURCE_TYPE]" class="custom-select mb-3" required>
                <option value="LINK" <?=$arResult['FIELDS']['RESOURCE_TYPE']['VALUE'] == 'LINK' ? 'selected' : ''?> >
                    Ссылка на файл
                </option>
                <option value="FILE" <?=$arResult['FIELDS']['RESOURCE_TYPE']['VALUE'] == 'FILE' ? 'selected' : ''?> >
                    Загрузить файл
                </option>
            </select>
        </div>

        <div class="alert alert-dark" role="alert">

            <div class="alert alert-warning" role="alert">
                Если выбран источник "Ссылка на файл"
            </div>
            <div class="form-group">
                <label>Ссылка на файл XML *</label>
                <input type="text" class="form-control" name="STORE_FIELD[FILE_LINK]" value="<?=$arResult['FIELDS']['FILE_LINK']['VALUE']?>" />
                <small class="form-text text-muted">Ссылка должна вести на файл формата <b>.xml</b>. Так же стоит
                    учитывать, что мы не принимаем файлы, генерируемые "на лету", т.к. время ожидания и скачивания файла
                    ограничено!<br />
                    Ограничение по размеру - <?=$component->intMaxDownloadXMLFileSizeMb?> Мб.
                </small>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="STORE_FIELD[HTTP_AUTH]" value="Y" id="customSwitch1" />
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
                <small class="form-text text-muted">Может быть загружен только XML. Ограничение по размеру
                    - <?=$component->intMaxUploadXMLFileSizeMb?> Мб.
                </small>
            </div>

        </div>

        <div class="form-group">
            <button class="btn btn-warning">
                <ion-icon name="add-circle-outline"></ion-icon>
                Добавить сайт
            </button>
        </div>

        <? if( $arResult['ADD_STATUS'] == 'ERROR' ): ?>
            <div class="alert alert-danger" role="alert">
                <?=implode(
                    '<br/>',
                    $arResult['ERROR_TEXT']
                )?>
            </div>
        <? endif; ?>

    </form>

<? endif; ?>


<a href="<?=\Local\Core\Inner\Route::getRouteTo(
    'store',
    'list',
    ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
)?>" class="btn btn-dark">
    <ion-icon name="arrow-round-back"></ion-icon>
    Вернуться к магазинам</a>

