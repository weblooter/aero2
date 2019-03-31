<?php

namespace Local\Core\Inner\Robofeed;


use Local\Core\Inner\Exception\FatalException;
use Local\Core\Inner\Robofeed\Exceptions\RobofeedNotUpdatedException;
use Local\Core\Inner\Route;
use Local\Core\Model\Data\CompanyTable;
use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Robofeed\ImportLogTable;

class ImportData
{
    /**
     * Импортирует базу
     *
     * @param $intStoreId
     *
     * @return \Bitrix\Main\Result
     * @throws \Bitrix\Main\ObjectException
     */
    public static function execute($intStoreId)
    {
        $obResult = new \Bitrix\Main\Result();
        $obWarning = new \Bitrix\Main\Result();

        $arLoggerData = [
            'STORE_ID' => $intStoreId,
            'DATE_CREATE' => new \Bitrix\Main\Type\DateTime()
        ];

        $strDownloadPath = $_SERVER['DOCUMENT_ROOT'].'/upload/tmp/local.core/robofeed/store_'.$intStoreId.'.xml';

        $arStoreImportData = [];
        $arStoreImportData['DATE_LAST_IMPORT'] = $arLoggerData['DATE_CREATE'];

        $rsStore = StoreTable::getByPrimary($intStoreId, ['filter' => ['ACTIVE' => 'Y']]);
        if( $rsStore->getSelectedRowsCount() > 0 )
        {
            $arStore = $rsStore->fetch();
            try
            {
                $strFilePath = self::getFilePath($arStore, $strDownloadPath);

                $obReader = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory(1);
                $obReader->setXmlPath($strFilePath);

                $arLoggerData['ROBOFEED_VERSION'] = $obReader->getRobofeedVersion();
                $arLoggerData['ROBOFEED_DATE'] = new \Bitrix\Main\Type\DateTime($obReader->getRobofeedLastModified(), 'Y-m-d H:i:s');
                $arLoggerData['BEHAVIOR_IMPORT_ERROR'] = $arStore['BEHAVIOR_IMPORT_ERROR'];
                $arLoggerData['NOT_UPDATED_XML_IS_ERROR'] = $arStore['NOT_UPDATED_XML_IS_ERROR'];

                $rsLastLog = ImportLogTable::getList(
                    [
                        'filter' => [
                            'STORE_ID' => $arLoggerData['STORE_ID'],
                        ],
                        'order' => ['ID' => 'DESC'],
                        'select' => ['ROBOFEED_VERSION', 'ROBOFEED_DATE'],
                        'limit' => 1,
                        'offset' => 0
                    ]
                );
                $isDouble = false;
                if( $rsLastLog->getSelectedRowsCount() > 0 )
                {
                    $arDouble = $rsLastLog->fetch();
                    if(
                        $arDouble['ROBOFEED_VERSION'] == $arLoggerData['ROBOFEED_VERSION']
                        && !is_null($arDouble['ROBOFEED_DATE'])
                    )
                    {
                        if( $arDouble['ROBOFEED_DATE']->getTimestamp() == $arLoggerData['ROBOFEED_DATE']->getTimestamp() )
                        {
                            $isDouble = true;
                        }
                    }
                }

                if( $isDouble )
                {
                    switch( $arStore['NOT_UPDATED_XML_IS_ERROR'] )
                    {
                        case 'Y':
                            throw new RobofeedNotUpdatedException('Время и версия в Robofeed XML не изменились, файл не нуждается в актуализации.');
                            break;
                        case 'N':
                            $arLoggerData['IMPORT_RESULT'] = 'NU';
                            $arLoggerData['ERROR_TEXT'] = 'Время и версия в Robofeed XML не изменились, файл не нуждается в актуализации.';
                            break;
                    }
                }
                else
                {

                    $obValidateResult = self::validateRobofeed($arLoggerData['ROBOFEED_VERSION'], $strFilePath);
                    if(
                        $obValidateResult->isSuccess()
                        || ( !$obValidateResult->isSuccess() && $arStore['BEHAVIOR_IMPORT_ERROR'] == 'IMPORT_ONLY_VALID' )
                    )
                    {

                        if( !$obValidateResult->isSuccess() )
                        {
                            $obWarning->addErrors($obValidateResult->getErrors());
                        }


                        \Local\Core\Inner\Robofeed\Importer\Factory::factory($arLoggerData['ROBOFEED_VERSION'])
                            ->setStoreId($intStoreId)
                            ->resetTables();

                        $obImportResult = self::importRobofeed($intStoreId, $arLoggerData['ROBOFEED_VERSION'], $strFilePath);
                        $arImportResult = $obImportResult->getData();
                        $arLoggerData['PRODUCT_TOTAL_COUNT'] = $arImportResult['PRODUCT_TOTAL_COUNT'];
                        $arLoggerData['PRODUCT_SUCCESS_IMPORT'] = $arImportResult['PRODUCT_SUCCESS_IMPORT'];
                    }
                    else
                    {
                        throw new \Exception(implode('<br/>', $obValidateResult->getErrorMessages()));
                    }

                    $arLoggerData['IMPORT_RESULT'] = 'SU';
                    $arStoreImportData['LAST_IMPORT_RESULT'] = 'SU';
                }
            }
            catch( FatalException $e )
            {
                $arLoggerData['ERROR_TEXT'] = $e->getMessage();
                $obResult->addError(new \Bitrix\Main\Error('Критическая ошибка:'));
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
                $arLoggerData['IMPORT_RESULT'] = 'ER';
                $arStoreImportData['LAST_IMPORT_RESULT'] = 'ER';
            }
            catch(RobofeedNotUpdatedException $e)
            {
                $arLoggerData['ERROR_TEXT'] = $e->getMessage();
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
                $arLoggerData['IMPORT_RESULT'] = 'ER';
            }
            catch( \Throwable $e )
            {
                $arLoggerData['ERROR_TEXT'] = $e->getMessage();
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
                $arLoggerData['IMPORT_RESULT'] = 'ER';
                $arStoreImportData['LAST_IMPORT_RESULT'] = 'ER';
            }
            finally
            {
                if( !$obWarning->isSuccess() )
                {
                    $arLoggerData['ERROR_TEXT'] = implode('<br/>', $obWarning->getErrorMessages());
                }
                if( file_exists($strDownloadPath) )
                {
                    unlink($strDownloadPath);
                }

                ImportLogTable::add($arLoggerData);
            }

            $arCompany = \Local\Core\Model\Data\CompanyTable::getByPrimary($arStore['COMPANY_ID'], ['select' => ['USER_OWN_ID']])
                ->fetch();
            $arUser = [];
            if( !empty($arCompany['USER_OWN_ID']) )
            {
                $arUser = \Bitrix\Main\UserTable::getByPrimary($arCompany['USER_OWN_ID'], ['select' => ['EMAIL']])
                    ->fetch();
            }

            switch( $arLoggerData['IMPORT_RESULT'] )
            {
                case 'ER':
                    if( !empty($arUser['EMAIL']) )
                    {
                        \Bitrix\Main\Mail\Event::send(
                            array(
                                "EVENT_NAME" => "LOCAL_IMPORT_ROBOFEED_ERROR",
                                "LID" => "s1",
                                "C_FIELDS" => array(
                                    "EMAIL" => $arUser['EMAIL'],
                                    'STORE_NAME' => $arStore['NAME'],
                                    'ERROR_MSG' => $arLoggerData['ERROR_TEXT']
                                )
                            )
                        );
                    }
                    break;
                case 'SU':
                    $arStoreImportData['DATE_LAST_SUCCESS_IMPORT'] = $arLoggerData['DATE_CREATE'];
                    $arStoreImportData['PRODUCT_TOTAL_COUNT'] = $arLoggerData['PRODUCT_TOTAL_COUNT'];
                    $arStoreImportData['PRODUCT_SUCCESS_IMPORT'] = $arLoggerData['PRODUCT_SUCCESS_IMPORT'];

                    if( !empty($arUser['EMAIL']) )
                    {

                        if( !empty($arLoggerData['ERROR_TEXT']) )
                        {
                            \Bitrix\Main\Mail\Event::send(
                                array(
                                    "EVENT_NAME" => "LOCAL_IMPORT_ROBOFEED_SUCCESS_WITH_WARNING",
                                    "LID" => "s1",
                                    "C_FIELDS" => array(
                                        "EMAIL" => $arUser['EMAIL'],
                                        'STORE_NAME' => $arStore['NAME'],
                                        'ERROR_MSG' => $arLoggerData['ERROR_TEXT']
                                    )
                                )
                            );
                        }
                        else
                        {
                            $intCount = 0;
                            $rs = ImportLogTable::getList(
                                [
                                    'filter' => [
                                        'STORE_ID' => $arStore['ID']
                                    ],
                                    'order' => ['ID' => 'DESC'],
                                    'limit' => 3,
                                    'offset' => 0,
                                    'select' => ['IMPORT_RESULT']
                                ]
                            );

                            while($a = $rs->fetch())
                            {
                                if( $a['IMPORT_RESULT'] == 'SU' )
                                    $intCount++;
                            }

                            if( $intCount == '1' )
                            {
                                \Bitrix\Main\Mail\Event::send(
                                    array(
                                        "EVENT_NAME" => "LOCAL_IMPORT_ROBOFEED_AGAIN_SUCCESS",
                                        "LID" => "s1",
                                        "C_FIELDS" => array(
                                            "EMAIL" => $arUser['EMAIL'],
                                            'STORE_NAME' => $arStore['NAME'],
                                            'STORE_LINK' => Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => $arStore['COMPANY_ID'], '#STORE_ID#' => $arStore['ID']])
                                        )
                                    )
                                );
                            }
                        }
                    }

                    break;
            }


            if( empty( $arStoreImportData['LAST_IMPORT_RESULT'] ) )
            {
                $arStoreImportData['LAST_IMPORT_RESULT'] = $arStore['LAST_IMPORT_RESULT'];
                if( $arStoreImportData['LAST_IMPORT_RESULT'] == 'SU' )
                {
                    $arStoreImportData['DATE_LAST_SUCCESS_IMPORT'] = $arLoggerData['DATE_CREATE'];
                }
            }

            StoreTable::update(
                $arStore['ID'],
                $arStoreImportData
            );
        }

        return $obResult;
    }

    private static function getFilePath($arStore, $strDownloadPath)
    {
        $strFilePath = null;
        switch( $arStore['RESOURCE_TYPE'] )
        {
            case 'LINK':
                $obHttp = new \Bitrix\Main\Web\HttpClient();
                $obHttp->setStreamTimeout(
                    \Bitrix\Main\Config\Configuration::getInstance()
                        ->get('store')['download_xml']['connect_timeout'] ?? 60
                );
                $intMaxFileSize = \Bitrix\Main\Config\Configuration::getInstance()
                                      ->get('store')['download_xml']['max_size_mb'] ?? 300;
                $obHttp->setBodyLengthMax($intMaxFileSize * 1024 * 1024);
                if( empty($arStore['FILE_LINK']) )
                {
                    throw new FatalException('Не указана ссылка на Robofeed XML');
                }

                if( $arStore['HTTP_AUTH'] == 'Y' )
                {
                    $obHttp->setAuthorization($arStore['HTTP_AUTH_LOGIN'], $arStore['HTTP_AUTH_PASS']);
                }

                if( $qqres = $obHttp->download($arStore['FILE_LINK'], $strDownloadPath) )
                {
                    switch( $obHttp->getStatus() )
                    {
                        case '404':
                            throw new FatalException('Не удалось скачать Robofeed XML. Файл не обнаружен, ответ сервера - 404');
                            break;
                        case '401':
                            throw new FatalException('Не удалось скачать Robofeed XML. Требуется авторизация, ответ сервера - 401');
                            break;
                        case '200':
                            $strFilePath = $strDownloadPath;
                            break;
                        default:
                            throw new FatalException('Скачивание и обработка Robofeed XML отменена. Ожидаемый ответ сервера - 200, текущий ответ сервера - '.$obHttp->getStatus());
                            break;
                    }
                }
                else
                {
                    if( $obHttp->getStatus() == 200 || $obHttp->getStatus() == 0 )
                    {
                        throw new FatalException(
                            'Не удалось скачать Robofeed XML. Напоминаем про ограничение скачиваемого файла по размеру ( до '.$intMaxFileSize.' мб ) и времени скачивания файла ( '
                            .( \Bitrix\Main\Config\Configuration::getInstance()
                                   ->get('store')['download_xml']['connect_timeout'] ?? 60 ).' сек. ). Проверьте размер и время отдачи файла.'
                        );
                    }
                    else
                    {
                        throw new FatalException('Не удалось скачать Robofeed XML. Ответ сервера - '.$obHttp->getStatus());
                    }
                }

                break;

            case 'FILE':
                if( $arStore['FILE_ID'] < 0 )
                {
                    throw new \Exception('У магазина выбран тип импорта "Загрузить файл", но файла не загружен');
                }

                $strFilePath = \Bitrix\Main\Application::getInstance()
                                   ->getContext()
                                   ->getServer()
                                   ->getDocumentRoot().\Local\Core\Inner\BxModified\CFile::GetPath($arStore['FILE_ID']);
                if( !file_exists($strFilePath) )
                {
                    throw new \Exception('Файл по пути "'.$strFilePath.'" не обнаружен.');
                }
                break;
        }

        return $strFilePath;
    }

    private static function validateRobofeed($intVersion, $strFilePath)
    {
        $obReader = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory($intVersion);
        $obReader->setScript(\Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader::SCRIPT_XSD_VALIDATE);
        $obReader->setXmlPath($strFilePath);

        return $obReader->run();
    }

    private static function importRobofeed($intStoreId, $intVersion, $strFilePath)
    {
        $obReader = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory($intVersion);
        $obReader->setScript(\Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader::SCRIPT_IMPORT);
        $obReader->setStoreId($intStoreId);
        $obReader->setXmlPath($strFilePath);

        return $obReader->run();
    }

    /**
     * Ищет и создает очередь на импорт
     */
    public static function createQueueToImportProducts()
    {
        $intTimeoutBetweenImportsInMin = \Bitrix\Main\Config\Configuration::getInstance()
                                             ->get('robofeed')['import']['timeout_between_import_robofeed'] ?? 360;
        $intTimestamp = ( new \Bitrix\Main\Type\DateTime() )->add('-'.$intTimeoutBetweenImportsInMin.' minutes')
            ->getTimestamp();

        $rsStoreToQueue = \Local\Core\Model\Data\StoreTable::getList(
            [
                'filter' => [
                    'ACTIVE' => 'Y'
                ],
                'select' => [
                    'ID',
                ],
                'order' => [
                    'DATE_CREATE' => 'DESC',
                ],
            ]
        );

        while( $arStore = $rsStoreToQueue->fetch() )
        {
            $boolNeedCreateJob = false;

            $arLastLog = \Local\Core\Model\Robofeed\ImportLogTable::getList(
                [
                    'filter' => ['STORE_ID' => $arStore['ID']],
                    'select' => ['ID', 'DATE_CREATE'],
                    'order' => ['DATE_CREATE' => 'DESC']
                ]
            )
                ->fetch();
            if( !empty($arLastLog['DATE_CREATE']) )
            {
                if( $arLastLog['DATE_CREATE']->getTimestamp() < $intTimestamp )
                {
                    $boolNeedCreateJob = true;
                }
            }
            else
            {
                $boolNeedCreateJob = true;
            }

            if( $boolNeedCreateJob )
            {
                $worker = new \Local\Core\Inner\JobQueue\Worker\StoreRobofeedImport(['STORE_ID' => $arStore['ID']]);
                $dateTime = new \Bitrix\Main\Type\DateTime();
                \Local\Core\Inner\JobQueue\Job::addIfNotExist($worker, $dateTime, 1);
            }
        }
    }
}