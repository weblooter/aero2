<?
/**
 * @var array                     $arParams
 * @var array                     $arResult
 * @var \PersonalSettingComponent $component
 * @var CBitrixComponentTemplate  $this
 * @var string                    $templateName
 * @var string                    $componentPath
 * @var string                    $templateFolder
 * @global CMain                  $APPLICATION
 */
?>
<div class="card">
    <div class="card-body">
        <form action="<?=\Bitrix\Main\Application::getInstance()
            ->getContext()
            ->getRequest()
            ->getRequestedPageDirectory()?>/" method="post" enctype="multipart/form-data">
            <?=bitrix_sessid_post()?>

            <?if( !empty($arResult['STATUS']) ):?>
                <?
                switch ($arResult['STATUS'])
                {
                    case 'ERROR':
                        ?>
                            <div class="alert alert-danger mb-4"><?=$arResult['ERROR_TEXT']?></div>
                        <?
                        break;
                    case 'SUCCESS':
                        ?>
                            <div class="alert alert-success mb-4">Данные успешно обновлены!</div>
                        <?
                        break;
                }
                ?>
            <?endif;?>

            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label><b>Имя * :</b></label>
                        <input type="text" name="USER_DATA[NAME]" class="form-control" value="<?=$arResult['NAME']?>" required />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label><b>Фамилия * :</b></label>
                        <input type="text" name="USER_DATA[LAST_NAME]" class="form-control" value="<?=$arResult['LAST_NAME']?>" required />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label><b>E-mail / Логин * :</b></label>
                        <input type="text" name="USER_DATA[EMAIL]" class="form-control" value="<?=$arResult['EMAIL']?>" required />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label>Новый аватар:</label>
                        <input type="file" name="IMAGE" class="file" data-show-preview="false" data-msg-placeholder="Загрузите файл" data-show-cancel="false" data-show-upload="false" />
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label>Новый пароль:</label>
                        <input type="password" name="USER_DATA[NEW_PASS]" class="form-control" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label>Повторите новый пароль:</label>
                        <input type="password" name="USER_DATA[NEW_PASS_REP]" class="form-control" />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label><b>Текущий пароль * :</b></label>
                        <input type="password" name="USER_DATA[OLD_PASS]" class="form-control" required />
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                    <div class="form-group">
                        <br />
                        <button type="submit" class="btn btn-secondary">Сохранить</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>