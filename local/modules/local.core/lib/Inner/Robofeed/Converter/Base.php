<?php
namespace Local\Core\Inner\Robofeed\Converter;


use Local\Core\Inner\Exception\FatalException;
use Local\Core\Inner\Route;
use Local\Core\Model\Robofeed\ConvertTable;

/**
 * Базовый класс конвертера.
 * Он же раннер.
 *
 * @package Local\Core\Inner\Robofeed\Converter
 */
class Base
{
    public static function execute($intId)
    {
        $obResult = new \Bitrix\Main\Result();

        $arConvert = ConvertTable::getByPrimary($intId)->fetch();
        if( !empty( $arConvert ) )
        {
            ConvertTable::update($intId, ['STATUS' => 'IN']);

            $obConverter = null;

            switch($arConvert['HANDLER'])
            {
                case 'YML':
                    $obConverter = new \Local\Core\Inner\Robofeed\Converter\YML();
                    break;
            }

            if(!is_null($obConverter) )
            {
                $arUpdateFields = [];
                try
                {
                    $newFileId = $obConverter->setFilePath($_SERVER['DOCUMENT_ROOT'].\Local\Core\Inner\BxModified\CFile::GetPath($arConvert['ORIGINAL_FILE_ID']))->execute();

                    $arUpdateFields = [
                        'EXPORT_FILE_ID' => $newFileId,
                        'STATUS' => 'SU'
                    ];

                }
                catch(\Throwable $e)
                {
                    $arUpdateFields = [
                        'STATUS' => 'ER',
                        'ERROR_MESSAGE' => $e->getMessage()
                    ];
                }

                if( $arUpdateFields['STATUS'] == 'SU' )
                {
                    try
                    {

                        $obReader = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory(1);
                        $obReader->setScript(\Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader::SCRIPT_XSD_VALIDATE);
                        $obReader->setXmlPath($_SERVER['DOCUMENT_ROOT'].\Local\Core\Inner\BxModified\CFile::GetPath( $arUpdateFields['EXPORT_FILE_ID'] ));
                        $obValidRes = $obReader->run();

                        if( !$obValidRes->isSuccess() )
                        {
                            \Local\Core\Inner\BxModified\CFile::Delete($newFileId);
                            throw new FatalException(implode('<br/>', $obValidRes->getErrorMessages()));
                        }
                    }
                    catch(\Throwable $e)
                    {
                        $arUpdateFields = [
                            'STATUS' => 'ER',
                            'VALID_ERROR_MESSAGE' => $e->getMessage()
                        ];
                    }
                }

                \Local\Core\Inner\BxModified\CFile::Delete($arConvert['ORIGINAL_FILE_ID']);
                ConvertTable::update($intId, $arUpdateFields);

                $arUser = \Bitrix\Main\UserTable::getList([
                    'filter' => [
                        'ID' => $arConvert['USER_ID']
                    ],
                    'select' => [
                        'EMAIL'
                    ]
                ])->fetch();
                if( !empty($arUser['EMAIL']) )
                {

                    $mailHeader = '';
                    $mailBody = '';

                    switch($arUpdateFields['STATUS'])
                    {
                        case 'SU':
                            $tmpRouteConvert = Route::getRouteTo('development', 'convert');
                            $tmpRouteStore = Route::getRouteTo('store', 'list');

                            $mailHeader = 'Ваш Robofeed XML готов!';
                            $mailBody = <<<DOCHERE
Мы сконвертировали Ваш файл в Robofeed XML! Он валиден и готов к импорту в нашу базу.
Скачать файл Вы можете в личном кабинете по ссылке <a href="https://robofeed.ru$tmpRouteConvert" target="_blank">https://robofeed.ru$tmpRouteConvert</a>.<br/>
После скачивания загрузите его в необходимый <a href="https://robofeed.ru$tmpRouteStore" target="_blank">магазин</a>.<br/>
Файл удалится через 4 часа.
DOCHERE;
                            break;

                        case 'ER':
                            $strBecause = '';
                            if( !empty( $arUpdateFields['ERROR_MESSAGE'] ) )
                            {
                                $strBecause = $arUpdateFields['ERROR_MESSAGE'].'<br/>Изучите <a href="https://robofeed.ru'.Route::getRouteTo('development', 'robofeed').'" target="_blank">как сделать Robofeed XML</a>.';
                            }
                            else if( !empty( $arUpdateFields['VALID_ERROR_MESSAGE'] ) )
                            {
                                $strBecause = 'Ваш файл содержит не все необходимые нам данные, из-за чего мы не смогли сконвертировать его в Robofeed XML. Изучите <a href="https://robofeed.ru/'.Route::getRouteTo('development', 'robofeed').'" target="_blank">как сделать Robofeed XML</a>.';
                            }

                            $mailHeader = 'Нам не удалось сделать Robofeed XML =(';
                            $mailBody = <<<DOCHERE
Нам не удалось сконвертировать Ваш файл в Robofeed XML.<br>
$strBecause
DOCHERE;
                            break;

                        default:
                            $tmpRouteConvert = Route::getRouteTo('development', 'convert');

                            $mailHeader = 'Мы проверили Ваш файл';
                            $mailBody = 'О результате Вы можете узнать в личном кабинете по ссылке <a href="https://robofeed.ru'.$tmpRouteConvert.'" target="_blank">https://robofeed.ru'.$tmpRouteConvert.'</a>';
                            break;
                    }

                    \Bitrix\Main\Mail\Event::send(
                        array(
                            "EVENT_NAME" => "LOCAL_YML_CONVERT_COMPLETED",
                            "LID" => "s1",
                            "C_FIELDS" => array(
                                "EMAIL" => $arUser['EMAIL'],
                                'MSG' => $mailBody,
                                'HEADER_MAIL' => $mailHeader
                            )
                        )
                    );
                }

            }
        }

        return $obResult;
    }

    public static function deleteOldCovert()
    {
        $rs = \Local\Core\Model\Robofeed\ConvertTable::getList(
            [
                'filter' => [
                    '<=DATE_MODIFIED' => ( new \Bitrix\Main\Type\DateTime() )->add('-'.( \Bitrix\Main\Config\Configuration::getInstance()->get('robofeed')['convert']['delete_file_after'] ?? 240 ).' minutes')
                ],
                'select' => [
                    'ID'
                ]
            ]
        );
        while($ar = $rs->fetch())
        {
            ConvertTable::delete($ar['ID']);
        }
    }
}