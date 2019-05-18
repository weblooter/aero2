<?
?>
<div class="row app-shortcuts">
    <?foreach ($arResult['ITEMS'] as $arLvl):?>
        <a class="col-4 app-shortcuts__item" href="<?=$arLvl['LINK']?>">
            <i class="<?=$arLvl['ICON_CLASS']?>"></i>
            <small class=""><?=$arLvl['TEXT']?></small>
        </a>
    <?endforeach;?>
</div>
