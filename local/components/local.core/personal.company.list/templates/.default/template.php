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

<? if (empty($arResult['ITEMS'])): ?>
    <p>У Вас не зарегистрировано ни одной компании.</p>
<? else: ?>
    <div class="row">
        <? foreach ($arResult['ITEMS'] as $arItem): ?>

            <div class="col-sm-6 col-md-3 mb-4">
                <div class="quick-stats__item d-block mb-0">
                    <div class="quick-stats__info">
                        <h2 class="mb-3">
                            <a class="quick-stats__link" href="<?=\Local\Core\Inner\Route::getRouteTo('store', 'list', [
                                '#COMPANY_ID#' => $arItem['ID']
                            ])?>"><?=$arItem['NAME']?></a>
                        </h2>

                        <div class="actions">
                            <div class="dropdown actions__item">
                                <i data-toggle="dropdown" class="zmdi zmdi-more-vert" aria-expanded="false"></i>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">
                                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'edit', ['#COMPANY_ID#' => $arItem['ID']])?>" class="dropdown-item"><i class="zmdi zmdi-edit zmdi-hc-fw"></i> Изменить</a>
                                    <a href="javascript:void(0)" onclick="PersonalCompanyListComponent.deleteCompany('<?=$arItem['ID']?>', '<?=htmlspecialchars($arItem['NAME'])?>')" class="dropdown-item"><i class="zmdi zmdi-delete zmdi-hc-fw"></i> Удалить</a>
                                </div>
                            </div>
                        </div>

                        <p class="mb-0">
                            <b>Дата создания:</b> <?=( ( $arItem['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime ) ? $arItem['DATE_CREATE']->format('Y-m-d H:i') : '-' )?>
                            <?if( !empty( $arItem['COMPANY_NAME_SHORT'] ) ):?>
                                <br/>
                                <b>Название организации:</b> <?=$arItem['COMPANY_NAME_SHORT']?>
                            <?endif;?>
                            <?if( !empty( $arItem['COMPANY_INN'] ) ):?>
                                <br/>
                                <b>ИНН:</b> <?=$arItem['COMPANY_INN']?>
                            <?endif;?>
                        </p>
                    </div>
                </div>
            </div>

        <? endforeach; ?>
    </div>
<? endif; ?>

<a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'add')?>" class="btn btn-warning"><i class="zmdi zmdi-plus zmdi-hc-fw"></i> Добавить компанию</a>