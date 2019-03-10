<?/** @var array $arResult */?>

<form action="" method="post">
    <?=bitrix_sessid_post();?>
    <div class="alert alert-warning">
        // TODO<br/>
        Тикет №26
    </div>

    <?if( $arResult['ADD_STATUS'] == 'SUCCESS' ):?>
        <div class="alert alert-success">
            Компания #<?=$arResult['COMPANY_ID']?> успешно создана.
        </div>
        <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'detail', [ '#COMPANY_ID#' => $arResult['COMPANY_ID'] ])?>" class="btn btn-warning" target="_self">Перейти к компании</a>
    <?else:?>

        <?if( $arResult['ADD_STATUS'] == 'ERROR' ):?>
            <div class="alert alert-danger">
                <?=implode('<br/>', $arResult['ADD_ERRORS'] )?>
            </div>
        <?endif;?>

        <?foreach ($arResult['FIELDS'] as $arItem):?>
            <div class="form-group">
                <label><?=$arItem['TITLE']?><?=( $arItem['IS_REQUIRED'] ? ' *' : '' )?></label>
                <input type="text" class="form-control" name="COMPANY_FIELD[<?=$arItem['CODE']?>]" <?=$arItem['IS_REQUIRED'] ? 'required' : ''?> value="<?=$arItem['VALUE']?>" />
            </div>
        <?endforeach;?>

        <div class="form-group">
            <button type="submit" class="btn btn-warning">Создать компанию</button>
        </div>

        <?if( $arResult['ADD_STATUS'] == 'ERROR' ):?>
            <div class="alert alert-danger">
                <?=implode('<br/>', $arResult['ADD_ERRORS'] )?>
            </div>
        <?endif;?>

    <?endif;?>
</form>
