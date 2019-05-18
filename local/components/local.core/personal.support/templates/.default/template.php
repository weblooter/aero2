<?
/**
 * @var array                     $arParams
 * @var array                     $arResult
 * @var \PersonalSupportComponent $component
 * @var CBitrixComponentTemplate  $this
 * @var string                    $templateName
 * @var string                    $componentPath
 * @var string                    $templateFolder
 * @global CMain                  $APPLICATION
 */
?>
<div class="messages mb-5">
    <div class="messages__sidebar">
        <div class="toolbar toolbar--inner">
            <div class="toolbar__label">Список обращений</div>
        </div>

        <div class="messages__search">
            <div class="form-group">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo('support', 'detail', ['#SUPPORT_ID#' => 0])?>" class="btn btn-outline-secondary btn-block">Создать обращение</a>
            </div>
        </div>

        <div class="listview listview--hover">
            <div class="scrollbar-inner">

                <?foreach ($arResult['SUPPORT_LIST'] as $arItem):?>
                    <a class="listview__item" href="<?=\Local\Core\Inner\Route::getRouteTo('support', 'detail', ['#SUPPORT_ID#' => $arItem['ID']])?>">
                        <div class="listview__content">
                            <div class="listview__heading">#<?=$arItem['ID']?></div>
                            <p>
                                Дата создания: <?=( $arItem['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime ? $arItem['DATE_CREATE']->format('Y.m.d H:i') : '-' )?><br />
                                Статус: <?
                                switch ($arItem['ACTIVE'])
                                {
                                    case 'Y':
                                        ?><span class="badge badge-success">Открыто</span><?
                                        break;
                                    case 'N':
                                        ?><span class="badge badge-light">Закрыто</span><?
                                        break;
                                }
                                ?>
                                <?if( !empty( $arItem['LAST_WRITER'] ) ):?>
                                    <br />
                                    Последнее сообщение от:
                                    <?
                                    switch ($arItem['LAST_WRITER'])
                                    {
                                        case 'US':
                                            ?><span class="badge badge-light">Вас</span><?
                                            break;
                                        case 'AD':
                                            ?><span class="badge badge-warning">Агента</span><?
                                            break;
                                    }
                                    ?>
                                <?endif;?>
                            </p>
                        </div>
                    </a>
                <?endforeach;?>

            </div>
        </div>
    </div>

    <div class="messages__body">
        <div class="messages__header">
            <div class="toolbar toolbar--inner">
                <div class="toolbar__label">
                    <?
                    if( $arParams['SUPPORT_ID'] < 0 )
                    {
                        ?>
                        Выберите обращение
                        <?
                    }
                    elseif( $arParams['SUPPORT_ID'] == 0 )
                    {
                        ?>
                        Новое обращение
                        <?
                    }
                    if( $arParams['SUPPORT_ID'] > 0 )
                    {
                        ?>
                        Обращение #<?=$arParams['SUPPORT_ID']?>
                        <?
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="messages__content">
            <div class="scrollbar-inner">

                <div class="message__history">
                    <?foreach ($arResult['SUPPORT_MSG'] as $arItem):?>
                        <div class="messages__item <?=( $arItem['OWN'] == 'AD' ? 'messages__item--right' : '' )?>">
                            <?if( $arItem['OWN'] == 'US' ):?>
                                <img src="<?=$arResult['USER']['IMG']?>" class="avatar-img" />
                            <?endif;?>
                            <div class="messages__details">
                                <p><?=$arItem['MSG']?></p>
                                <small><i class="zmdi zmdi-time"></i> <?=( $arItem['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime ? $arItem['DATE_CREATE']->format('Y/m/d в H:i') : '-' )?></small>
                            </div>
                        </div>
                    <?endforeach;?>
                </div>

                <?if( !empty( $arResult['SUPPORT_MSG'] ) && $arResult['SUPPORT_LIST'][ $arParams['SUPPORT_ID'] ]['ACTIVE'] == 'N' ):?>
                <div class="alert alert-warning text-center">
                    <small>
                        Обращение закрыто. Отправьте сообщение и оно откроется автоматически.
                    </small>
                </div>
                <?endif;?>

                <?if( $arParams['SUPPORT_ID'] >= 0 ):?>
                    <div class="messages__item w-100">
                        <img src="<?=$arResult['USER']['IMG']?>" class="avatar-img" />

                        <div class="messages__details w-100">
                            <textarea class="form-control textarea-autosize" placeholder="Напишите текст" data-support-message></textarea>
                            <a href="javascript:void(0)" class="btn btn-secondary text-dark" onclick="PersonalSupportComponent.sendMessage();"><i class="zmdi zmdi-mail-send"></i> Отправить</a>
                            <?if( !empty( $arResult['SUPPORT_MSG'] ) && $arResult['SUPPORT_LIST'][ $arParams['SUPPORT_ID'] ]['ACTIVE'] == 'Y' ):?>
                                <a href="javascript:void(0)" class="btn btn-light" onclick="PersonalSupportComponent.closeTask();"><i class="zmdi zmdi-lock-outline"></i> Закрыть обращение</a>
                            <?endif;?>
                        </div>
                    </div>
                <?endif;?>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    PersonalSupportComponent.setSupportId(<?=$arParams['SUPPORT_ID']?>);
    PersonalSupportComponent.setSupportListUrl('<?=\Local\Core\Inner\Route::getRouteTo('support', 'list')?>');
</script>