<?
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var \PersonalCompanyFormEditComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */
?>

<form action="" method="post">
    <?=bitrix_sessid_post();?>
    <div class="alert alert-warning">
        // TODO<br/>
        Тикет №26
    </div>

    <?if( $arResult['UPDATE_STATUS'] == 'SUCCESS' ):?>
        <div class="alert alert-success">
            Данные успешно обновлены!
        </div>
    <?endif;?>
    <?if( $arResult['UPDATE_STATUS'] == 'ERROR' ):?>
        <div class="alert alert-danger">
            <?=implode('<br/>', $arResult['UPDATE_ERRORS'] )?>
        </div>
    <?endif;?>

    <?foreach ($arResult['FIELDS'] as $arItem):?>
        <div class="form-group">
            <label><?=$arItem['TITLE']?><?=( $arItem['IS_REQUIRED'] ? ' *' : '' )?></label>
            <input type="text" class="form-control" name="COMPANY_FIELD[<?=$arItem['CODE']?>]" <?=$arItem['IS_REQUIRED'] ? 'required' : ''?> value="<?=$arItem['VALUE']?>" />
        </div>
    <?endforeach;?>

    <div class="form-group">
        <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'detail', [ '#COMPANY_ID#' => \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('COMPANY_ID') ])?>" class="btn btn-dark">Вернуться к компании</a>
        <button type="submit" class="btn btn-warning">Сохранить изменения</button>
    </div>

    <?if( $arResult['UPDATE_STATUS'] == 'SUCCESS' ):?>
        <div class="alert alert-success">
            Данные успешно обновлены!
        </div>
    <?endif;?>
    <?if( $arResult['UPDATE_STATUS'] == 'ERROR' ):?>
        <div class="alert alert-danger">
            <?=implode('<br/>', $arResult['UPDATE_ERRORS'] )?>
        </div>
    <?endif;?>

</form>
