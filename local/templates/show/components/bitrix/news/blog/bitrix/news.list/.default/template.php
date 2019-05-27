<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="robofeed-news__page">
    <?
    $arResult["ITEMS"] = array_chunk($arResult["ITEMS"], 2);
    ?>
    <?foreach ($arResult["ITEMS"] as $arRow):?>
        <div class="row">
            <?foreach ($arRow as $arItem):?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                $arItem["PREVIEW_PICTURE"]["SRC"] = \CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"]["ID"], ['width' => 9999, 'height' => 200], BX_RESIZE_IMAGE_EXACT, false, false, false, 75);
                ?>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 robofeed-news__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="img" style="background-image: url('<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>');"></a>
                    <p>
                        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a>
                    </p>
                    <?=$arItem["PREVIEW_TEXT"];?>
                    <small class="date"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></small>
                </div>
            <?endforeach;?>
        </div>
    <?endforeach;?>

    <div class="clearfix"></div>
    <?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
        <?=$arResult["NAV_STRING"]?>
    <?endif;?>
</div>