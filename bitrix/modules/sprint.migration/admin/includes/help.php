<?php
global $APPLICATION;
$isSettinsPage = strpos($APPLICATION->GetCurPage(), 'settings.php');
?>
<div class="sp-group">
    <div class="sp-group-row2">
        <div class="sp-block">
            <? if ($isSettinsPage): ?>
                <a href="/bitrix/admin/sprint_migrations.php?config=cfg&lang=<?= LANGUAGE_ID ?>"><?= GetMessage('SPRINT_MIGRATION_GOTO_MIGRATION') ?></a>
            <? else: ?>
                <a href="/bitrix/admin/settings.php?mid=sprint.migration&mid_menu=1&lang=<?= LANGUAGE_ID ?>"><?= GetMessage('SPRINT_MIGRATION_GOTO_OPTIONS') ?></a>
            <? endif; ?>
        </div>
        <div class="sp-block">
            <div style="margin-bottom: 10px;">
                <?= GetMessage('SPRINT_MIGRATION_LINK_MP') ?> <br/>
                <a href="http://marketplace.1c-bitrix.ru/solutions/sprint.migration/" target="_blank">http://marketplace.1c-bitrix.ru/solutions/sprint.migration/</a>
            </div>
            <div style="margin-bottom: 10px;">
                <?= GetMessage('SPRINT_MIGRATION_LINK_COMPOSER') ?>
                <br/>
                <a href="https://packagist.org/packages/andreyryabin/sprint.migration" target="_blank">https://packagist.org/packages/andreyryabin/sprint.migration</a>
            </div>
            <div style="margin-bottom: 10px;">
                <?= GetMessage('SPRINT_MIGRATION_LINK_DOC') ?>
                <br/>
                <a href="https://github.com/andreyryabin/sprint.migration/wiki" target="_blank">https://github.com/andreyryabin/sprint.migration/wiki</a>
            </div>
            <div style="margin-bottom: 10px;">
                <?= GetMessage('SPRINT_MIGRATION_LINK_ARTICLES') ?>
                <br/>
                <a href="https://dev.1c-bitrix.ru/search/?tags=sprint.migration" target="_blank">https://dev.1c-bitrix.ru/search/?tags=sprint.migration</a>
            </div>
        </div>
    </div>
</div>