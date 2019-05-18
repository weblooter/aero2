<?
$obCache = \Bitrix\Main\Application::getInstance()->getCache();

if(
    $obCache->startDataCache(
        60*60*24*7,
        __FILE__.'#'.$GLOBALS['USER']->GetID(),
        \Local\Core\Inner\Cache::getCachePath(['Other', 'UserAsideInfoInTemplate'], ['userId='.$GLOBALS['USER']->GetID()])
    )
)
{
    $arUser = \Bitrix\Main\UserTable::getByPrimary($GLOBALS['USER']->GetID(), ['select' => ['NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_PHOTO']])->fetch();

    if( $arUser['PERSONAL_PHOTO'] > 0 )
    {
        $arTmp = \Local\Core\Inner\BxModified\CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], ['width' => 40, 'height' => 40], BX_RESIZE_IMAGE_EXACT, false, false, false, 75);
        $arUser['IMG'] = $arTmp['src'];
    }
    else
    {
        $arUser['IMG'] = SITE_TEMPLATE_PATH.'/assets/img/user-image.png';
    }

    $obCache->endDataCache($arUser);
}
else
{
    $arUser = $obCache->getVars();
}
?>
<img class="user__img" src="<?=$arUser['IMG']?>" />
<div class="user__wrapper">
    <div class="user__name"><?=$arUser['NAME']?> <?=$arUser['LAST_NAME']?></div>
    <div class="user__email"><?=$arUser['EMAIL']?></div>
</div>