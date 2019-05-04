<?
/**
 * @global CMain                      $APPLICATION
 * @var array                         $arParams
 * @var array                         $arResult
 * @var \PersonalCompanyFormComponent $component
 * @var CBitrixComponentTemplate      $this
 * @var string                        $templateName
 * @var string                        $componentPath
 * @var string                        $templateFolder
 */
?>

<div class="card">
    <div class="card-body">
        <? if ($arResult['STATUS'] == 'ADD_SUCCESS'): ?>
            <div class="alert alert-success">
                Компания успешно создана!
            </div>
            <script type="text/javascript">
                setTimeout(function () {
                    location.href = "<?=\Local\Core\Inner\Route::getRouteTo('store', 'list', ['#COMPANY_ID#' => $arResult['ADD_ID']])?>";
                }, 1500);
            </script>
        <? else: ?>

            <form action="<?=\Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest()
                ->getRequestedPageDirectory()?>/" method="post" id="companyformcomponent">
                <?=bitrix_sessid_post();?>
                <? if ($arResult['STATUS'] == 'UPDATE_SUCCESS'): ?>
                    <div class="alert alert-success">
                        Данные успешно обновлены!
                    </div>
                <? endif; ?>
                <? if ($arResult['STATUS'] == 'ERROR'): ?>
                    <div class="alert alert-danger">
                        <?=implode('<br/>', $arResult['ERROR_TEXT'])?>
                    </div>
                <? endif; ?>


                <? foreach ($arResult['FIELDS']['GENERAL_FIELDS'] as $k => $v): ?>
                    <?
                    switch ($k) {
                        case 'TYPE':
                            ?>
                            <div class="form-group">
                                <label><?=$v['TITLE']?><?=($v['IS_REQUIRED'] ? ' *' : '')?> :</label>
                                <select class="select2" name="COMPANY_FIELD[<?=$v['CODE']?>]" <?=$v['IS_REQUIRED'] ? 'required' : ''?> data-minimum-results-for-search="Infinity" data-company-type-value onchange="PersonalCompanyFormComponent.changeBlock();">
                                    <?
                                    foreach (\Local\Core\Model\Data\CompanyTable::getEnumFieldHtmlValues($k) as $k1 => $v1):?>
                                        <option value="<?=$k1?>" <?=($k1 == $v['VALUE']) ? 'selected' : ''?> ><?=$v1?></option>
                                    <? endforeach; ?>
                                </select>
                            </div>
                            <?
                            break;
                        default:
                            ?>
                            <div class="form-group">
                                <label><?=$v['TITLE']?><?=($v['IS_REQUIRED'] ? ' *' : '')?></label>
                                <input type="text" class="form-control" name="COMPANY_FIELD[<?=$v['CODE']?>]" <?=$v['IS_REQUIRED'] ? 'required' : ''?> value="<?=$v['VALUE']?>" />
                            </div>
                            <?
                            break;
                    }
                    ?>
                <? endforeach; ?>

                <div class="d-none" data-company-type="UR">
                    <h4 class="card-title">Данный о юридическом лице</h4>

                    <div class="form-group">
                        <label>ИНН * :</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="COMPANY_FIELD[COMPANY_INN]" value="<?=$arResult['FIELDS']['COMPANY']['BASE_FIELDS']['COMPANY_INN']['VALUE']?>" data-inn-field />
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="PersonalCompanyFormComponent.searchCompanyByInn();"><i class="zmdi zmdi-search zmdi-hc-fw"></i> Искать</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Если Вы заполните ИНН, то мы попробуем найти компанию, что позволит частично заполнить форму в автоматическом режиме.
                        </small>
                    </div>

                    <? foreach ($arResult['FIELDS']['COMPANY']['BASE_FIELDS'] as $arItem): ?>
                        <?
                        if( $arItem['CODE'] == 'COMPANY_INN' )
                            continue;
                        ?>
                        <div class="form-group">
                            <label><?=$arItem['TITLE']?><?=($arItem['IS_REQUIRED'] ? ' * ' : '')?>:</label>
                            <input type="text" class="form-control" name="COMPANY_FIELD[<?=$arItem['CODE']?>]" value="<?=$arItem['VALUE']?>" />
                        </div>
                    <? endforeach; ?>
                    <h4 class="card-title">Фактический адрес</h4>
                    <? foreach ($arResult['FIELDS']['COMPANY']['ADDRESS'] as $arItem): ?>
                        <div class="form-group">
                            <label><?=$arItem['TITLE']?><?=($arItem['IS_REQUIRED'] ? ' * ' : '')?>:</label>
                            <input type="text" class="form-control" name="COMPANY_FIELD[<?=$arItem['CODE']?>]" value="<?=$arItem['VALUE']?>" />
                        </div>
                    <? endforeach; ?>
                </div>

                <? if ($arResult['STATUS'] == 'UPDATE_SUCCESS'): ?>
                    <div class="alert alert-success">
                        Данные успешно обновлены!
                    </div>
                <? endif; ?>
                <? if ($arResult['STATUS'] == 'ERROR'): ?>
                    <div class="alert alert-danger">
                        <?=implode('<br/>', $arResult['ERROR_TEXT'])?>
                    </div>
                <? endif; ?>

                <? if ($arParams['COMPANY_ID'] > 0): ?>
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'detail', [
                        '#COMPANY_ID#' => $arParams['COMPANY_ID']
                    ])?>" class=" btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться к компании</a>
                    <button type="submit" class="btn btn-warning">Сохранить изменения</button>
                <? else: ?>
                    <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'list')?>" class="btn btn-dark"><i class="zmdi zmdi-arrow-back"></i> Вернуться к списку компаний</a>
                    <button type="submit" class="btn btn-warning">Добавить компанию</button>
                <? endif; ?>

                <script type="text/javascript">
                    PersonalCompanyFormComponent.init();
                </script>
            </form>

        <? endif; ?>
    </div>
</div>
