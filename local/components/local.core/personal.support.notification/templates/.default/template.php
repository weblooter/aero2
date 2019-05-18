<?
/**
 * @var array                     $arParams
 * @var array                     $arResult
 * @var \PersonalSupportNotificationComponent $component
 * @var CBitrixComponentTemplate  $this
 * @var string                    $templateName
 * @var string                    $componentPath
 * @var string                    $templateFolder
 * @global CMain                  $APPLICATION
 */
?>

<a href="javascript:void(0)" data-toggle="dropdown" class="<?=( !empty( $arResult['ITEMS'] ) ? 'top-nav__notify' : '' )?>"><i class="zmdi zmdi-email"></i></a>
<div class="dropdown-menu dropdown-menu-right dropdown-menu--block">
    <div class="dropdown-header">
        Новые сообщения

        <div class="actions">
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('support', 'detail', ['#SUPPORT_ID#' => 0])?>" class="actions__item zmdi zmdi-plus" title="Создать обращение"></a>
        </div>
    </div>

    <div class="listview listview--hover">
        <?if( empty($arResult['ITEMS']) ):?>
            <div class="listview__item">
                <div class="listview__content">
                    <p>Новых сообщений небыло</p>
                </div>
            </div>
        <?endif;?>
        <?foreach ($arResult['ITEMS'] as $arItem):?>
            <a href="<?=\Local\Core\Inner\Route::getRouteTo('support', 'detail', ['#SUPPORT_ID#' => $arItem['ID']])?>" class="listview__item">
                <div class="listview__content">
                    <div class="listview__heading">
                        Агент <small><?=( $arItem['LAST_COMMENT']['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime ? $arItem['LAST_COMMENT']['DATE_CREATE']->format('Y/m/d в H:i') : '-' )?></small>
                    </div>
                    <p><?=( new \CTextParser() )->html_cut($arItem['LAST_COMMENT']['MSG'], 200);?></p>
                </div>
            </a>
            <script type="text/javascript">
                document.addEventListener('DOMContentLoaded', function () {
                    LocalCore.notify('Получен ответ от поддержки по обращению #<?=$arItem['ID']?>', '<a href="<?=\Local\Core\Inner\Route::getRouteTo('support', 'detail', ['#SUPPORT_ID#' => $arItem['ID']])?>" class="btn btn-secondary btn-sm">Читать</a>');
                })
            </script>
        <?endforeach;?>

        <a href="<?=\Local\Core\Inner\Route::getRouteTo('support', 'list')?>" class="view-more">Все обращения</a>
    </div>
</div>
