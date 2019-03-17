<?
/**
 * @global CMain                   $APPLICATION
 * @var array                      $arParams
 * @var array                      $arResult
 * @var \PersonalSiteListComponent $component
 * @var CBitrixComponentTemplate   $this
 * @var string                     $templateName
 * @var string                     $componentPath
 * @var string                     $templateFolder
 */
?>
<div class="col-12">

    <? if( empty($arResult['ITEMS']) ): ?>
        <p>К компании не привязан ни один сайт</p>
    <? else: ?>
        <? foreach( $arResult['ITEMS'] as $arItem ): ?>

            <div class="media">
                <div class="media-body">
                    <div class="pull-right">
                        <a href="<?=\Local\Core\Inner\Route::getRouteTo(
                            'site',
                            'edit',
                            [
                                '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                                '#SITE_ID#' => $arItem['ID']
                            ]
                        )?>" title="Редактировать">
                            <ion-icon name="create"></ion-icon>
                        </a>
                        <a href="#" title="Удалить">
                            <ion-icon name="trash"></ion-icon>
                        </a>
                    </div>
                    <h5 class="mt-0">
                        <a href="<?=\Local\Core\Inner\Route::getRouteTo(
                            'site',
                            'detail',
                            [
                                '#COMPANY_ID#' => $arParams['COMPANY_ID'],
                                '#SITE_ID#' => $arItem['ID']
                            ]
                        )?>">[<?=$arItem['ID']?>] <?=$arItem['NAME']?></a>
                    </h5>
                    Активность: <?=( $arItem['ACTIVE'] == 'Y' ) ? 'Активен' : 'Деактивирован'?><br />
                    Дата создания: <?=date(
                        'Y-m-d H:i:s',
                        $arItem['DATE_CREATE']->getTimestamp()
                    )?><br />
                    Источник
                    данных: <?=( $arItem['RESOURCE_TYPE'] == 'LINK' ) ? 'Ссылка на файл' : 'Загружен файл'?>

                    <?
                    // TODO Сделать вывод последней проверки
                    ?>
                    <div class="alert alert-warning" role="alert">
                        //TODO<br />
                        Статус последней проверки: Ожидает проверки<br />
                        Дата последней проверки: -
                    </div>
                </div>
            </div>
            <hr />

        <? endforeach; ?>
    <? endif; ?>

    <a href="<?=\Local\Core\Inner\Route::getRouteTo(
        'site',
        'add',
        ['#COMPANY_ID#' => $arParams['COMPANY_ID']]
    )?>" class="btn btn-warning">+
        Добавить сайт</a>

    <?
    $APPLICATION->IncludeComponent(
        "bitrix:main.pagenavigation",
        "",
        array(
            "NAV_OBJECT" => $arResult['NAV_OBJ'],
            "SEF_MODE" => "N", // ЧПУ пагинация или нет, Y|N
            "SHOW_COUNT" => "N",
        ),
        false
    );
    ?>

</div>