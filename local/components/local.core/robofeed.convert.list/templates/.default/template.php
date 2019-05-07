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
    <p>
        У Вас нет файлов на конветирование в очереди.
    </p>
<? else: ?>
    <table class="table table-inverse">
        <thead>
        <tr>
            <th width="20%">Дата и файл</th>
            <th width="20%">Обработчик</th>
            <th colspan="2">Результат</th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($arResult['ITEMS'] as $arItem): ?>
            <tr>
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
                            echo '<br/><a class="btn btn-outline-secondary" href="'.\Bitrix\Main\Application::getInstance()
                                    ->getContext()
                                    ->getRequest()
                                    ->getRequestedPageDirectory().'/?getFile='.urlencode(\Local\Core\Inner\BxModified\CFile::GetPath($arItem['EXPORT_FILE_ID'])).'" target="_blank"><i class="zmdi zmdi-cloud-download zmdi-hc-fw"></i> Скачать файл</a>';
                            break;
                        case 'ER':

                            if (!empty($arItem['ERROR_MESSAGE'])) {
                                echo '<br/>'.$arItem['ERROR_MESSAGE'];
                            }

                            if (!empty($arItem['VALID_ERROR_MESSAGE'])) {
                                echo '<br/>Ваш файл содержит не все необходимые нам данные, из-за чего мы не смогли сконвертировать его в Robofeed XML.<a href="'
                                     .Route::getRouteTo('development', 'robofeed').'" class="btn btn-outline-secondary" target="_blank">Как сделать Robofeed XML?</a><br/><br/>
Так же Вы можете скачать то, что получилось и попробовать загрузить его в магазин, выставив у поля <b>"Поведение импорта при ошибке"</b> значение <b>"Актуализировать только валидные"</b>.<br/>
<a class="btn btn-outline-secondary" href="'.\Bitrix\Main\Application::getInstance()
                                         ->getContext()
                                         ->getRequest()
                                         ->getRequestedPageDirectory().'/?getFile='.urlencode(\Local\Core\Inner\BxModified\CFile::GetPath($arItem['EXPORT_FILE_ID'])).'" target="_blank"><i class="zmdi zmdi-cloud-download zmdi-hc-fw"></i> Скачать что получилось</a>
<button class="btn btn-light" type="button" data-toggle="collapse" data-target="#collapse'.$arItem['ID'].'">Смотреть ошибки валидации</button>
<div class="collapse" id="collapse'.$arItem['ID'].'"><br/>
    <div class="card card-body mb-0">'.$arItem['VALID_ERROR_MESSAGE'].'</div>
</div>

';
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