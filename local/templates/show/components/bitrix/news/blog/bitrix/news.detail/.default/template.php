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
<h1><?=$arResult["NAME"]?></h1>
<div class="robofeed-news__detail">
    <div class="wallpapper" style="background-image: url('<?=$arResult['PREVIEW_PICTURE']?>')"></div>
    <p>
        <small class="date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></small>
    </p>
    <?=$arResult["DETAIL_TEXT"];?>
    <br/>
    <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
    <script src="//yastatic.net/share2/share.js"></script>
    <div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,moimir,twitter,linkedin,lj,viber,whatsapp,skype,telegram"></div>
    <br/>
    <br/>
</div>
