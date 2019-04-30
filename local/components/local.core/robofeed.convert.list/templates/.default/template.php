<?
/**
 * @global CMain                      $APPLICATION
 * @var array                         $arParams
 * @var array                         $arResult
 * @var \RobofeedConvertFormComponent $component
 * @var CBitrixComponentTemplate      $this
 * @var string                        $templateName
 * @var string                        $componentPath
 * @var string                        $templateFolder
 */

use Local\Core\Inner\Route;

?>
<? if (empty($arResult['ITEMS'])): ?>
<div class="alert alert-success">У Вас нет файлов на конветирование в очереди.</div>
<? else: ?>
    <table class="table">
        <thead>
        <tr>
            <th width="20%">Дата и файл</th>
            <th width="20%">Обработчик</th>
            <th colspan="2">Результат</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($arResult['ITEMS'] as $arItem): ?>
            <?
            $strStatusColor = null;
            switch ($arItem['STATUS']) {
                case 'SU':
                    $strStatusColor = 'table-success';
                    break;
                case 'ER':
                    $strStatusColor = 'table-danger';
                    break;
                case 'IN':
                    $strStatusColor = 'table-info';
                    break;
                default:
                    $strStatusColor = 'table-dark';
                    break;
            }
            ?>
            <tr class="<?=$strStatusColor?>">
                <td>
                    <?=date('Y-m-d H:i:s', $arItem['DATE_MODIFIED']->getTimestamp())?><br />
                    <b><?=$arItem['ORIGINAL_FILE_NAME']?></b>
                </td>
                <td><?=$arResult['HANDLER'][$arItem['HANDLER']]?></td>
                <td>
                    <b><?=$arResult['STATUS'][$arItem['STATUS']]?></b>
                    <?
                    switch ($arItem['STATUS']) {
                        case 'SU':
                            echo '<br/><a class="btn btn-warning" href="'.\Bitrix\Main\Application::getInstance()
                                    ->getContext()
                                    ->getRequest()
                                    ->getRequestedPageDirectory().'/?getFile='.urlencode(\Local\Core\Inner\BxModified\CFile::GetPath($arItem['EXPORT_FILE_ID'])).'" target="_blank">Скачать файл</a>';
                            break;
                        case 'ER':

                            if (!empty($arItem['ERROR_MESSAGE'])) {
                                echo '<br/>'.$arItem['ERROR_MESSAGE'];
                            }

                            if (!empty($arItem['VALID_ERROR_MESSAGE'])) {
                                echo '<br/>Ваш файл содержит не все необходимые нам данные, из-за чего мы не смогли сконвертировать его в Robofeed XML. Изучите <a href="'
                                     .Route::getRouteTo('development', 'robofeed').'" target="_blank">как сделать Robofeed XML</a>.<br/>
Так же Вы можете скачать то, что получилось и попробовать загрузить его в магазин, выставив у поля <b>"Поведение импорта при ошибке"</b> значение <b>"Актуализировать только валидные"</b>.<br/>
<a class="btn btn-warning" href="'.\Bitrix\Main\Application::getInstance()
                                         ->getContext()
                                         ->getRequest()
                                         ->getRequestedPageDirectory().'/?getFile='.urlencode(\Local\Core\Inner\BxModified\CFile::GetPath($arItem['EXPORT_FILE_ID'])).'" target="_blank">Скачать что получилось</a><br/>
<details><summary>Ошибки валидации:</summary><div>'.$arItem['VALID_ERROR_MESSAGE'].'</div></details>';
                            }
                            break;
                    }
                    ?>
                </td>
            </tr>
        <? endforeach; ?>
        </tbody>
    </table>

<? endif; ?>