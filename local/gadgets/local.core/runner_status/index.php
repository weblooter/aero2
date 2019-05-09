<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}


?>
<div class="bx-gadgets-info">
    <div class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t" style="font-weight: bold; line-height: 28px;"><?=$sProduct;?></div>
    <div style="margin: 0 1px 0 1px; border-bottom: 1px solid #D7E0E8;"></div>
    <div class="bx-gadgets-content-padding-rl">
        <table class="bx-gadgets-info-site-table">
            <tr>
                <td>
                    <? dump(\Local\Core\Inner\JobQueue\Runner::getRunnerStatus()); ?>
                </td>
            </tr>
        </table>
        <h4>Текущие запущенные процессы:</h4>
        <?
        $ar = \Local\Core\Inner\JobQueue\Runner::getExecutedProcesses();
        if( !empty( $ar ) )
        {
            foreach ($ar as $arItem)
            {
                if( $arItem['LAST_EXECUTE_START'] instanceof \Bitrix\Main\Type\DateTime)
                {
                    if( ( strtotime('now') - $arItem['LAST_EXECUTE_START']->getTimestamp() ) >= 60*25 )
                    {
                        ?>
                        <div style="background: #d12; padding: 10px;">
                            <h4 style="color: #fff">Запущен более 25 минут назад (<?=$arItem['LAST_EXECUTE_START']->format('Y-m-d H:i:s')?>)</h4>
                            <?dump($arItem)?>
                        </div>
                        <?
                    }
                    else if( ( strtotime('now') - $arItem['LAST_EXECUTE_START']->getTimestamp() ) >= 60*15 )
                    {
                        ?>
                        <div style="background: orange; padding: 10px;">
                            <h4 style="color: #fff">Запущен более 15 минут назад (<?=$arItem['LAST_EXECUTE_START']->format('Y-m-d H:i:s')?>)</h4>
                            <?dump($arItem)?>
                        </div>
                        <?
                    }
                    else
                    {
                        ?>
                        <div style="background: #22bb22; padding: 10px;">
                            <h4 style="color: #fff">Запущен менее 15 минут назад (<?=$arItem['LAST_EXECUTE_START']->format('Y-m-d H:i:s')?>)</h4>
                            <?dump($arItem)?>
                        </div>
                        <?
                    }
                }
                else
                {
                    ?>
                    <div style="background: gray; padding: 10px;">
                        <h4 style="color: #fff">Время запуска отсутствует, но он запущен</h4>
                        <?dump($arItem)?>
                    </div>
                    <?
                }
            }
        }
        ?>
    </div>
</div>