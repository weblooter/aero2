<?
/**
 * @global CMain                      $APPLICATION
 * @var array                         $arParams
 * @var array                         $arResult
 * @var \PersonalCompanyListComponent $component
 * @var CBitrixComponentTemplate      $this
 * @var string                        $templateName
 * @var string                        $componentPath
 * @var string                        $templateFolder
 */
?>
<div class="col-12">

    <? if( $arResult['ITEMS'] > 0 ): ?>
        <? foreach( $arResult['ITEMS'] as $arItem ): ?>

            <?
            $strAlertClass = '';
            switch( $arItem['VERIFIED'] )
            {
                case 'Y':
                    $strAlertClass = 'alert-success';
                    break;
                case 'N':
                    $strAlertClass = 'alert-dark';
                    break;
                case 'E':
                    $strAlertClass = 'alert-danger';
                    break;
            }
            ?>
            <div class="alert <?=$strAlertClass?>">
                <div class="pull-right">
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo(
                        'company',
                        'edit',
                        ['#COMPANY_ID#' => $arItem['ID']]
                    )?>" title="Редактировать">
                        <ion-icon name="create"></ion-icon>
                    </a>
                    <a href="#" title="Удалить">
                        <ion-icon name="trash"></ion-icon>
                    </a>
                </div>
                <b><a href="<?=\Local\Core\Inner\Route::getRouteTo(
                        'company',
                        'detail',
                        ['#COMPANY_ID#' => $arItem['ID']]
                    )?>">[<?=$arItem['ID']?>
                        ] <?=$arItem['COMPANY_NAME_SHORT']?> (ИНН: <?=$arItem['COMPANY_INN']?>)</a></b><br />
                Дата создания: <?=date(
                    'Y.m.d H:i:s',
                    $arItem['DATE_CREATE']->getTimestamp()
                )?><br />
                Активность: <?=$arItem['ACTIVE'] == 'Y' ? 'Активна' : 'Деактивирована'?><br />
                Верификация:
                <?
                switch( $arItem['VERIFIED'] )
                {
                    case 'Y':
                        ?>
                        Компания успешно верифицированна!
                        <?
                        break;
                    case 'N':
                        ?>
                        Компания еще не проходила проверку.
                        <?
                        break;
                    case 'E':
                        ?>
                        Во время проверки компании возникли проблемы: <?=$arItem['VERIFIED_NOTE']?><br />
                        До тех пор, пока ошибки не будут исправлены, компания и ее сайты не будут работать.
                        <?
                        break;
                }
                ?>
            </div>

        <? endforeach; ?>
    <? else: ?>
        <p>
            У Вас нет ни одной созданной компании!
        </p>
    <? endif; ?>
    <a href="/personal/company/add/" class="btn btn-warning">+ Добавить компанию</a>

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