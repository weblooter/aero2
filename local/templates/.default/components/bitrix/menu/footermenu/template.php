<? /** @var array $arResult */ ?>
<ul class="footermenu">
    <? foreach ($arResult as $arItem): ?>
        <li class="<?=($arItem['SELECTED'] > 0) ? 'active' : ''?>">
            <a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a>
        </li>
    <? endforeach; ?>
</ul>