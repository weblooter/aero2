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

        $arConvert = ConvertTable::getByPrimary($intId)
            ->fetch();
        if (!empty($arConvert)) {
            ConvertTable::update($intId, ['STATUS' => 'IN']);

            $obConverter = null;

            switch ($arConvert['HANDLER']) {
                case 'YML':
                    $obConverter = new \Local\Core\Inner\Robofeed\Converter\YML();
                    break;
            }

            if (!is_null($obConverter)) {
                $arUpdateFields = [];
                try {
                    $newFileId = $obConverter->setFilePath($_SERVER['DOCUMENT_ROOT'].\Local\Core\Inner\BxModified\CFile::GetPath($arConvert['ORIGINAL_FILE_ID']))
                        ->execute();

                    $arUpdateFields = [
                        'EXPORT_FILE_ID' => $newFileId,
                        'STATUS' => 'SU'
                    ];

                } catch (\Throwable $e) {
                    $arUpdateFields = [
                        'STATUS' => 'ER',
                        'ERROR_MESSAGE' => $e->getMessage()
                    ];
                }

                if ($arUpdateFields['STATUS'] == 'SU') {
                    try {

                        $obReader = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory(1);
                        $obReader->setScript(\Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader::SCRIPT_XSD_VALIDATE);
                        $obReader->setXmlPath($_SERVER['DOCUMENT_ROOT'].\Local\Core\Inner\BxModified\CFile::GetPath($arUpdateFields['EXPORT_FILE_ID']));
                        $obValidRes = $obReader->run();

                        if (!$obValidRes->isSuccess()) {
                            //\Local\Core\Inner\BxModified\CFile::Delete($newFileId);
                            throw new FatalException(implode('<br/>', $obValidRes->getErrorMessages()));
                        }
                    } catch (\Throwable $e) {
                        $arUpdateFields = [
                            'STATUS' => 'ER',
                            'EXPORT_FILE_ID' => $newFileId,
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
                ])
                    ->fetch();
                if (!empty($arUser['EMAIL'])) {
                    $arEmailFields = [
                        'EMAIL' => $arUser['EMAIL'],
                        'HOW_MADE_ROBOFEED_ROUTE' => Route::getRouteTo('development', 'robofeed'),
                        'STORE_ROUTE' => Route::getRouteTo('store', 'list'),
                        'CONVERT_ROUTE' => Route::getRouteTo('tools', 'converter'),
                    ];
                    switch ($arUpdateFields['STATUS']) {
                        case 'SU':
                            $arEmailFields['STATUS'] = 'SU';
                            $arEmailFields['HEADER_MAIL'] = 'Ваш Robofeed XML готов!';
                            break;

                        case 'ER':
                            if (!empty($arUpdateFields['ERROR_MESSAGE'])) {
                                $arEmailFields['STATUS'] = 'ER';
                                $arEmailFields['ERROR_MESSAGE'] = $arUpdateFields['ERROR_MESSAGE'];
                            } else {
                                if (!empty($arUpdateFields['VALID_ERROR_MESSAGE'])) {
                                    $arEmailFields['STATUS'] = 'VAER';
                                    $arEmailFields['ERROR_MESSAGE'] = $arUpdateFields['VALID_ERROR_MESSAGE'];
                                }
                            }

                            $arEmailFields['HEADER_MAIL'] = 'Нам не удалось сделать Robofeed XML =(';

                            break;

                        default:

                            $arEmailFields['STATUS'] = 'OTHER';
                            $arEmailFields['HEADER_MAIL'] = 'Мы проверили Ваш файл';
                            break;
                    }

                    \Local\Core\Inner\TriggerMail\Robofeed\Convert::convertCompleted($arEmailFields);
                }

            }
        }

        return $obResult;
    }

    public static function deleteOldCovert()
    {
        $rs = \Local\Core\Model\Robofeed\ConvertTable::getList([
            'filter' => [
                '<=DATE_MODIFIED' => (new \Bitrix\Main\Type\DateTime())->add('-'.(\Bitrix\Main\Config\Configuration::getInstance()
                                                                                      ->get('robofeed')['convert']['delete_file_after'] ?? 240).' minutes')
            ],
            'select' => [
                'ID'
            ]
        ]);
        while ($ar = $rs->fetch()) {
            ConvertTable::delete($ar['ID']);
        }
    }
}