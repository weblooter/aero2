<?
/**
 * @var array                    $arParams
 * @var array                    $arResult
 * @var \MainpageCalcComponent   $component
 * @var CBitrixComponentTemplate $this
 * @var string                   $templateName
 * @var string                   $componentPath
 * @var string                   $templateFolder
 * @global CMain                 $APPLICATION
 */
?>
<section id="tarifs" class="tarifsblock" data-start="<?=$arResult["START_ELEM"]["ITERATOR"]?>" data-values='<?=$arResult["VALUES"]?>'>
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
                                <? $GLOBALS['APPLICATION']->IncludeFile($this->GetFolder().'/include/tp-list.php') ?>
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
                                    <p class="subtitle">до <?=number_format($arResult["START_ELEM"]["LIMIT"], 0, '.', ' ')?> товаров</p>
                                    <? if ($arResult["START_ELEM"]["PRICE_OLD"]) { ?>
                                        <p class="price old" data-price="<?=$arResult["START_ELEM"]["PRICE_OLD"]?>"><span><?=number_format($arResult["START_ELEM"]["PRICE_OLD"], 0, '.', ' ')?></span>
                                            руб. / месяц</p>
                                    <? } ?>
                                    <p class="price" data-price="<?=$arResult["START_ELEM"]["PRICE"]?>">
                                        <?if( $arResult["START_ELEM"]["PRICE"] > 0 ):?>
                                            <span><?=number_format($arResult["START_ELEM"]["PRICE"], 0, '.', ' ')?></span> руб. / месяц
                                        <?else:?>
                                            Бесплатно
                                        <?endif;?>
                                    </p>
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
                            <? $GLOBALS['APPLICATION']->IncludeFile($this->GetFolder().'/include/tp-list.php') ?>
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
                                <p class="subtitle">до <?=number_format($arResult["START_ELEM"]["LIMIT"], 0, '.', ' ')?> товаров</p>
                                <? if ($arResult["START_ELEM"]["PRICE_OLD"]) { ?>
                                    <p class="price old" data-price="<?=$arResult["START_ELEM"]["PRICE_OLD"]?>"><span><?=number_format($arResult["START_ELEM"]["PRICE_OLD"], 0, '.', ' ')?></span> руб.
                                        / месяц</p>
                                <? } ?>
                                <p class="price" data-price="<?=$arResult["START_ELEM"]["PRICE"]?>">
                                    <?if( $arResult["START_ELEM"]["PRICE"] > 0 ):?>
                                        <span><?=number_format($arResult["START_ELEM"]["PRICE"], 0, '.', ' ')?></span> руб. / месяц
                                    <?else:?>
                                        Бесплатно
                                    <?endif;?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?
            foreach ($arResult["ITEMS"] as $key => $arItem) {
                ?>
                <div class="tarif" data-tarif="<?=$arItem["LIMIT"]?>">
                    <div class="block">
                        <div class="image" style="background-image: url('<?=$this->GetFolder()?>/images/<?=$arItem["CODE"]?>.png')"></div>
                        <h4><?=$arItem["NAME"]?></h4>
                        <p class="subtitle">
                            до <?=number_format($arItem["LIMIT"], 0, '.', ' ')?> товаров
                        </p>
                        <? if ($arItem["PRICE_OLD"]) {
                            ?>
                            <p class="price old" data-price="<?=$arItem["PRICE_OLD"]?>"><span>0</span> руб. / месяц</p>
                        <? } ?>
                        <p class="price" data-price="<?=$arItem["PRICE"]?>"><?=( $arItem["PRICE"] > 0 ? '<span>0</span> руб. / месяц' : 'Бесплатно' )?></p>
                    </div>
                </div>
            <? } ?>
            <? $last = end($arResult["ITEMS"]); ?>
            <div class="tarif" data-tarif="> <?=$last["LIMIT"]?>">
                <div class="block">
                    <div class="image" style="background-image: url('<?=$this->GetFolder()?>/images/<?=$last["CODE"]?>.png')"></div>
                    <h4>Индивидуальное предложение</h4>
                    <p class="subtitle">Более <?=number_format($last["LIMIT"], 0,'.', ' ')?> товаров</p>
                    <p class="price">Индивидуально</p>
                </div>
            </div>
        </div>
    </div>
</section>