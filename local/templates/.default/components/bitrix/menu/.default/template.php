<? /** @var array $arResult */ ?>
<ul class="nav nav-pills mt-3">
    <li class="nav-item">
        <a class="nav-link btn <?= \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory() == '' ? 'btn-warning' : '' ?>" href="/">Главная</a>
    </li>
    <? foreach ( $arResult as $arItem ): ?>
        <li class="nav-item">
            <a class="nav-link btn <?= ( $arItem[ 'SELECTED' ] > 0 ) ? 'btn-warning' : '' ?>" href="<?= $arItem[ 'LINK' ] ?>"><?= $arItem[ 'TEXT' ] ?></a>
        </li>
    <? endforeach; ?>
</ul>
<hr />
