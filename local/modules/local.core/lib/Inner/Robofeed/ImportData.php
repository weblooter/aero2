<?php

namespace Local\Core\Inner\Robofeed;


use Local\Core\Inner\Exception\FatalException;
use Local\Core\Model\Data\StoreTable;
use Local\Core\Model\Robofeed\ImportLogTable;
use Local\Core\Model\Robofeed\StoreCategoryFactory;
use Local\Core\Model\Robofeed\StoreProductFactory;

class ImportData
{
    public static function execute($intStoreId)
    {
        $obResult = new \Bitrix\Main\Result();
        $obWarning = new \Bitrix\Main\Result();

        $arLoggerData = [
            'STORE_ID' => $intStoreId,
            'DATE_CREATE' => new \Bitrix\Main\Type\DateTime()
        ];

        $strDownloadPath = $_SERVER['DOCUMENT_ROOT'].'/upload/tmp/local.core/robofeed/store_'.$intStoreId.'.xml';

        try
        {

            $rsStore = StoreTable::getById($intStoreId);
            if( $rsStore->getSelectedRowsCount() < 1 )
            {
                throw new \Exception('Магазин с ID "'.$intStoreId.'" не обнаружен');
            }

            $arStore = $rsStore->fetch();

            if( $arStore['ACTIVE'] != 'Y' )
            {
                throw new \Exception('Для актуализации данных необходимо активировать магазин');
            }

            $strFilePath = self::getFilePath($arStore, $strDownloadPath);

            $obReader = \Local\Core\Inner\Robofeed\XMLReader\Factory::factory(1);
            $obReader->setXmlPath($strFilePath);

            $arLoggerData['ROBOFEED_VERSION'] = $obReader->getRobofeedVersion();
            $arLoggerData['ROBOFEED_DATE'] = new \Bitrix\Main\Type\DateTime($obReader->getRobofeedLastModified(), 'Y-m-d H:i:s');
            $arLoggerData['BEHAVIOR_IMPORT_ERROR'] = $arStore['BEHAVIOR_IMPORT_ERROR'];

            // TODO раскомментировать после проверки
            /*
            $rsLastLog = ImportLogTable::getList([
                'filter' => [
                    'STORE_ID' => $arLoggerData['STORE_ID'],
                    'ROBOFEED_VERSION' => $arLoggerData['ROBOFEED_VERSION'],
                    'ROBOFEED_DATE' => $arLoggerData['ROBOFEED_DATE'],
                ]
            ]);

            if( $rsLastLog->getSelectedRowsCount() > 0 )
            {
                throw new \Exception('Время и версия в Robofeed XML не изменились, файл не нуждается в актуализации');
            }
            */

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

            $arLoggerData['IMPORT_COMPLETED'] = 'Y';
        }
        catch( FatalException $e )
        {
            $arLoggerData['ERROR_TEXT'] = $e->getMessage();
            $obResult->addError(new \Bitrix\Main\Error('Критическая ошибка'));
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            $arLoggerData['IMPORT_COMPLETED'] = 'E';
        }
        catch( \Exception $e )
        {
            $arLoggerData['ERROR_TEXT'] = $e->getMessage();
            $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            $arLoggerData['IMPORT_COMPLETED'] = 'E';
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

        return $obResult;
    }

    private static function getFilePath($arStore, $strDownloadPath)
    {
        $strFilePath = null;
        switch( $arStore['RESOURCE_TYPE'] )
        {
            case 'LINK':
                $obHttp = new \Bitrix\Main\Web\HttpClient();
                $obHttp->setStreamTimeout( \Bitrix\Main\Config\Configuration::getInstance()->get('store')['download_xml']['connect_timeout'] ?? 30 );
                $intMaxFileSize = \Bitrix\Main\Config\Configuration::getInstance()->get('store')['download_xml']['max_size_mb'] ?? 300;
                $obHttp->setBodyLengthMax( $intMaxFileSize * 1024 * 1024 );
                if( empty($arStore['FILE_LINK']) )
                {
                    throw new FatalException('Не указана ссылка на Robofeed XML');
                }

                if( $arStore['HTTP_AUTH'] == 'Y' )
                {
                    $obHttp->setAuthorization($arStore['HTTP_AUTH_LOGIN'], $arStore['HTTP_AUTH_PASS']);
                }

                if( $obHttp->download($arStore['FILE_LINK'], $strDownloadPath) )
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
                    if( $obHttp->getStatus() == 200 )
                    {
                        throw new FatalException('Не удалось скачать Robofeed XML. Напоминаем про ограничение скачиваемого файла по размеру ( до '.$intMaxFileSize.' мб ) и времени скачивания файла ( '.( \Bitrix\Main\Config\Configuration::getInstance()->get('store')['download_xml']['connect_timeout'] ?? 30 ).' сек. ). Проверьте размер и время отдачи файла.');
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
}