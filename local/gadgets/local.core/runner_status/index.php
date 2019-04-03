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
    </div>
</div>