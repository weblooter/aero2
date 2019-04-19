<?
/**
 * @global CMain                      $APPLICATION
 * @var array                         $arParams
 * @var array                         $arResult
 * @var \MainpageCalcComponent        $component
 * @var CBitrixComponentTemplate      $this
 * @var string                        $templateName
 * @var string                        $componentPath
 * @var string                        $templateFolder
 */
?>
<section class="tarifsblock" data-start="<?=$arResult["START_ELEM"]["ITERATOR"]?>" data-values='<?=$arResult["VALUES"]?>'>
    <div class="container">
        <h2 class="bold">Расчет стоимости</h2>
        <div class="calculator">
            <div class="shops">
                <div class="shoprow row">
                    <div class="col-md-6 col-sm-6">
                        <div class="shop">
                            <h4>Количество товаров</h4>
                            <input type="text" class="range">
                            <h4>Торговые площадки</h4>
                            <div class="row">
                                <div class="col-sm-4 col-xs-6">
                                    <input type="checkbox" class="calccheckbox" id="platform1-1" checked>
                                    <label for="platform1-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform1.svg);"></label>
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <input type="checkbox" class="calccheckbox" id="platform2-1">
                                    <label for="platform2-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform2.svg);"></label>
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <input type="checkbox" class="calccheckbox" id="platform3-1">
                                    <label for="platform3-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform3.svg);"></label>
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <input type="checkbox" class="calccheckbox" id="platform4-1">
                                    <label for="platform4-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform4.svg);"></label>
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <input type="checkbox" class="calccheckbox" id="platform5-1">
                                    <label for="platform5-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform5.svg);"></label>
                                </div>
                                <div class="col-sm-4 col-xs-6">
                                    <input type="checkbox" class="calccheckbox" id="platform6-1">
                                    <label for="platform6-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform6.svg);"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 col-md-offset-1 col-sm-6">
                        <h4>Ваш тариф:</h4>
                        <div class="tarifswrap">
                            <div class="tarif">
                                <div class="block">
                                    <div class="image" style="background-image: url('<?=$this->GetFolder()?>/images/<?=$arResult["START_ELEM"]["CODE"]?>.png')"></div>
                                    <h4><?=$arResult["START_ELEM"]["NAME"]?></h4>
                                    <p class="subtitle"><?=$arResult["START_ELEM"]["LIMIT_FROM"]?> &mdash; <?=$arResult["START_ELEM"]["LIMIT"]?> товаров</p>
                                    <?if($arResult["START_ELEM"]["PRICE_OLD"]){?>
                                        <p class="price old" data-price="<?=$arResult["START_ELEM"]["PRICE_OLD"]?>"><span><?=$arResult["START_ELEM"]["PRICE_OLD"]?></span> руб. / месяц</p>
                                    <?}?>
                                    <p class="price" data-price="<?=$arResult["START_ELEM"]["PRICE"]?>"><span><?=$arResult["START_ELEM"]["PRICE"]?></span> руб. / месяц</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="addshop"><span>Добавить магазин</span></div>
        </div>
        <div class="calcsources hidden row">
            <div class="shoprow row">
                <div class="col-md-6 col-sm-6">
                    <div class="shop">
                        <h4>Количество товаров</h4>
                        <input type="text" class="range">
                        <h4>Торговые площадки</h4>
                        <div class="row">
                            <div class="col-sm-4 col-xs-6">
                                <input type="checkbox" class="calccheckbox" id="platform1-1" checked>
                                <label for="platform1-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform1.svg);"></label>
                            </div>
                            <div class="col-sm-4 col-xs-6">
                                <input type="checkbox" class="calccheckbox" id="platform2-1">
                                <label for="platform2-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform2.svg);"></label>
                            </div>
                            <div class="col-sm-4 col-xs-6">
                                <input type="checkbox" class="calccheckbox" id="platform3-1">
                                <label for="platform3-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform3.svg);"></label>
                            </div>
                            <div class="col-sm-4 col-xs-6">
                                <input type="checkbox" class="calccheckbox" id="platform4-1">
                                <label for="platform4-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform4.svg);"></label>
                            </div>
                            <div class="col-sm-4 col-xs-6">
                                <input type="checkbox" class="calccheckbox" id="platform5-1">
                                <label for="platform5-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform5.svg);"></label>
                            </div>
                            <div class="col-sm-4 col-xs-6">
                                <input type="checkbox" class="calccheckbox" id="platform6-1">
                                <label for="platform6-1" style="background-image: url(<?=SITE_TEMPLATE_PATH?>/assets/images/platform6.svg);"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-md-offset-1 col-sm-6">
                    <h4>Ваш тариф:</h4>
                    <div class="tarifswrap">
                        <div class="tarif">
                            <div class="block">
                                <div class="image" style="background-image: url('<?=$this->GetFolder()?>/images/<?=$arResult["START_ELEM"]["CODE"]?>.png')"></div>
                                <h4><?=$arResult["START_ELEM"]["NAME"]?></h4>
                                <p class="subtitle"><?=$arResult["START_ELEM"]["LIMIT_FROM"]?> &mdash; <?=$arResult["START_ELEM"]["LIMIT"]?> товаров</p>
                                <?if($arResult["START_ELEM"]["PRICE_OLD"]){?>
                                <p class="price old" data-price="<?=$arResult["START_ELEM"]["PRICE_OLD"]?>"><span><?=$arResult["START_ELEM"]["PRICE_OLD"]?></span> руб. / месяц</p>
                                <?}?>
                                <p class="price" data-price="<?=$arResult["START_ELEM"]["PRICE"]?>"><span><?=$arResult["START_ELEM"]["PRICE"]?></span> руб. / месяц</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?
            foreach($arResult["ITEMS"] as $key => $arItem){?>
                <div class="tarif" data-tarif="<?=$arItem["LIMIT"]?>">
                    <div class="block">
                        <div class="image" style="background-image: url('<?=$this->GetFolder()?>/images/<?=$arItem["CODE"]?>.png')"></div>
                        <h4><?=$arItem["NAME"]?></h4>
                        <p class="subtitle"><?if($key == 0){echo '1';}else{echo $arResult["ITEMS"][$key-1]["LIMIT"];}?> &mdash; <?=$arItem["LIMIT"]?> товаров</p>
                        <?if($arItem["PRICE_OLD"]){?>
                        <p class="price old" data-price="<?=$arItem["PRICE_OLD"]?>"><span>0</span> руб. / месяц</p>
                        <?}?>
                        <p class="price" data-price="<?=$arItem["PRICE"]?>"><span>0</span> руб. / месяц</p>
                    </div>
                </div>
            <?}?>
            <?$last = end($arResult["ITEMS"]);?>
            <div class="tarif" data-tarif="> <?=$last["LIMIT"]?>">
                <div class="block">
                    <div class="image" style="background-image: url('<?=$this->GetFolder()?>/images/<?=$last["CODE"]?>.png')"></div>
                    <h4>Индивидуальное предложение</h4>
                    <p class="subtitle">Более <?=$last["LIMIT"]?> товаров</p>
                    <p class="price">Индивидуально</p>
                </div>
            </div>
        </div>
    </div>
</section>