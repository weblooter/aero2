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

<? if ($arResult['STATUS'] == 'ADD_SUCCESS'): ?>
    <div class="alert alert-success">
        Компания успешно создана!
    </div>
    <script type="text/javascript">
        setTimeout(function () {
            location.href = "<?=\Local\Core\Inner\Route::getRouteTo('company', 'detail', ['#COMPANY_ID#' => $arResult['ADD_ID']])?>";
        }, 3000);
    </script>
<? else: ?>

    <form action="<?=\Bitrix\Main\Application::getInstance()
        ->getContext()
        ->getRequest()
        ->getRequestedPageDirectory()?>/" method="post">
        <?=bitrix_sessid_post();?>
        <div class="alert alert-warning">
            // TODO<br />
            Тикет №26
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


        <? foreach ($arResult['FIELDS']['GENERAL_FIELDS'] as $k => $v): ?>
            <?
            switch ($k) {
                case 'TYPE':
                    ?>
                    <div class="form-group">
                        <label><?=$v['TITLE']?><?=($v['IS_REQUIRED'] ? ' *' : '')?></label>
                        <select class="form-control" name="COMPANY_FIELD[<?=$v['CODE']?>]" <?=$v['IS_REQUIRED'] ? 'required' : ''?>>
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


        <div class="alert alert-warning" role="alert">
            // Только для юр. лиц. Для физиков полей нет, кроме названия, все данные возьмем от юзвера.<br />
            <br />

            <h4>Данный о юридическом лице</h4>
            <? foreach ($arResult['FIELDS']['COMPANY']['BASE_FIELDS'] as $arItem): ?>
                <div class="form-group">
                    <label><?=$arItem['TITLE']?><?=($arItem['IS_REQUIRED'] ? ' *' : '')?></label>
                    <input type="text" class="form-control" name="COMPANY_FIELD[<?=$arItem['CODE']?>]" value="<?=$arItem['VALUE']?>" />
                </div>
            <? endforeach; ?>
            <h4>Фактический адрес</h4>
            <? foreach ($arResult['FIELDS']['COMPANY']['ADDRESS'] as $arItem): ?>
                <div class="form-group">
                    <label><?=$arItem['TITLE']?><?=($arItem['IS_REQUIRED'] ? ' *' : '')?></label>
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
            <div class="form-group">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'detail', [
                        '#COMPANY_ID#' => $arParams['COMPANY_ID']
                    ])?>" class="btn btn-dark">Вернуться
                    к компании</a>
                <button type="submit" class="btn btn-warning">Сохранить изменения</button>
            </div>
        <? else: ?>
            <div class="form-group">
                <a href="<?=\Local\Core\Inner\Route::getRouteTo('company', 'list')?>" class="btn btn-dark">Вернуться
                    к списку компаний</a>
                <button type="submit" class="btn btn-warning">Добавить компанию</button>
            </div>
        <? endif; ?>

    </form>

<? endif; ?>
