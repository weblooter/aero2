<?
/**
 * @global CMain                  $APPLICATION
 * @var array                     $arParams
 * @var array                     $arResult
 * @var \PersonalAsideMenuComponent $component
 * @var CBitrixComponentTemplate  $this
 * @var string                    $templateName
 * @var string                    $componentPath
 * @var string                    $templateFolder
 */
?>

<ul class="navigation">
    <?foreach ($arResult['ITEMS'] as $arLvl):?>
        <?$component::createLvlItem($arLvl, 0);?>
    <?endforeach;?>
</ul>
