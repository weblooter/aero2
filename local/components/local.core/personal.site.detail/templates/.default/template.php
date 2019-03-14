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

<div class="container-fluid">
    <div class="row">

        <div class="col-12">

            Сайт: <?=$arResult['ITEM']['DOMAIN'];?><br />
            Активность: <?=$arResult['ITEM']['ACTIVE'] == 'Y' ? 'Да' : 'Нет';?><br />
            <?
            switch( $arResult['ITEM']['RESOURCE_TYPE'] )
            {
                case 'LINK':
                    ?>
                    Источник данных: Ссылка на файл XML<br />
                    Для доступа нужен логин и пароль: <?=$arResult['ITEM']['HTTP_AUTH'] == 'Y' ? 'Да' : 'Нет'?>
                    <?
                    if( $arResult['ITEM']['HTTP_AUTH'] == 'Y' ):?>
                        <br />
                        Логин для авторизации: <?=$arResult['ITEM']['HTTP_AUTH_LOGIN']?><br />
                        Пароль для авторизации: <?=$arResult['ITEM']['HTTP_AUTH_PASS']?>
                    <? endif; ?>
                    <?
                    break;

                case 'FILE':
                    ?>
                    Источник данных: Загруженный файл XML<br />
                    <?
                    $strFileLink = \Local\Core\Inner\BxModified\CFile::GetPath($arResult['ITEM']['FILE_ID']);
                    ?>
                    Файл: <a href="<?=$strFileLink?>" target="_blank"><?=$strFileLink?></a>
                    <?
                    break;
            }
            ?>

        </div>

        <div class="col-6">
            <div class="alert alert-primary" role="alert">
                // TODO<br />
                Список выбранных фидов<br />
                <ion-icon name="done-all"></ion-icon>
                Яндекс маркет, Готов к выгрузке
                <hr />
                <ion-icon name="warning"></ion-icon>
                Беру ру, Необходимо проставить соответствия
                <hr />
                <ion-icon name="hourglass"></ion-icon>
                Озон, Проверяется
                <hr />
                <a href="#">Список фидов</a>
            </div>
        </div>
        <div class="col-6">
            <div class="alert alert-info" role="alert">
                // TODO<br />
                Вывод логов сайта, последние 10 результатов парсинга
            </div>
        </div>

    </div>
</div>
