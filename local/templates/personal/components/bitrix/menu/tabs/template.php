<? /** @var array $arResult */ ?>
<div class="col-12">
    <ul class="nav  nav-tabs mt-1 mb-3">
        <li class="nav-item">
            <a class="nav-link <?= \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory() == '/personal' ? 'active' : '' ?>" href="/personal/">Рабочий
                стол</a>
        </li>
        <? foreach ( $arResult as $arItem ): ?>
            <li class="nav-item">
                <a class="nav-link <?= ( $arItem[ 'SELECTED' ] > 0 ) ? 'active' : '' ?>" href="<?= $arItem[ 'LINK' ] ?>"><?= $arItem[ 'TEXT' ] ?></a>
            </li>
        <? endforeach; ?>
    </ul>
</div>
<div class="clearfix"></div>