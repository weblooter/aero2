<?php


namespace Local\Core\Inner\TradingPlatform;

use Bitrix\Main\UserTable;
use Local\Core\Inner\Route;

/**
 * Класс экспорта ТП
 *
 * @package Local\Core\Inner\TradingPlatform
 */
class Export
{
    /**
     * Запускает процесс формирования экспортного файла для ТП
     *
     * @param integer $intId ID ТП
     * @param bool $boolSendMailIfError Отправлять письмо об ошибке или нет
     *
     * @return \Bitrix\Main\Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function execute($intId, $boolSendMailIfError = false)
    {
        $obResult = new \Bitrix\Main\Result();
        $arFilledCheckErrors = [];

        if( \Local\Core\Model\Data\TradingPlatformTable::getList(['filter' => ['ID' => $intId], 'select' => ['ID']])->getSelectedRowsCount() > 0 )
        {

            $obTp = (new \Local\Core\Inner\TradingPlatform\TradingPlatform());
            try {
                $obTp->load($intId);
                $obHandler = $obTp->getHandler();

                $obCheckResult = $obHandler->isRulesTradingPlatformCorrectFilled();
                if( $obCheckResult->isSuccess() )
                {
                    $obRes = $obHandler->makeExportFile();
                    if( $obRes->isSuccess() )
                    {
                        $obResult->setData($obRes->getData());
                    }
                    else
                    {
                        $obResult->addErrors($obRes->getErrors());
                    }
                }
                else
                {
                    $arFilledCheckErrors = $obCheckResult->getErrorMessages();
                }

            } catch (\Local\Core\Inner\TradingPlatform\Exceptions\TradingPlatformNotFoundException $e) {
                $obResult->addError(new \Bitrix\Main\Error('Не удалось загрузить торговую площадку.'));
            } catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e) {
                $obResult->addError(new \Bitrix\Main\Error('Не удалось загрузить обработчик торговой площадки.'));
            } catch (\Throwable $e) {
                $obResult->addError(new \Bitrix\Main\Error($e->getMessage()));
            }
            finally
            {
                $arLog = [
                    'STORE_ID' => \Local\Core\Inner\TradingPlatform\Base::getStoreId($intId),
                    'TP_ID' => $intId,
                    'RESULT' => $obResult->isSuccess() && empty($arFilledCheckErrors) ? 'SU' : 'ER',
                    'PRODUCTS_TOTAL' => !empty( $obResult->getData()['PRODUCTS_TOTAL'] ) ? $obResult->getData()['PRODUCTS_TOTAL'] : 0,
                    'PRODUCTS_EXPORTED' => !empty( $obResult->getData()['PRODUCTS_EXPORTED'] ) ? $obResult->getData()['PRODUCTS_EXPORTED'] : 0
                ];
                if( !$obResult->isSuccess() )
                {
                    $arLog['ERROR_TEXT'] = implode('<br/>', $obResult->getErrorMessages());
                }

                if( $obResult->isSuccess() && empty($arFilledCheckErrors) && (\Local\Core\Model\Data\TradingPlatformExportLogTable::getList(['filter' => ['TP_ID' => $intId, 'RESULT' => 'SU']]))->getSelectedRowsCount() < 1 )
                {
                    $arFilledCheckErrors = array_merge($arFilledCheckErrors, $obResult->getErrorMessages());
                    $intStoreID = \Local\Core\Inner\TradingPlatform\Base::getStoreId($intId);
                    $arUser = \Bitrix\Main\UserTable::getByPrimary(\Local\Core\Inner\Store\Base::getOwnUserId($intStoreID), ['select' => ['EMAIL']])->fetch();

                    if( !empty( $arUser['EMAIL'] ) )
                    {
                        \Local\Core\Inner\TriggerMail\TradingPlatform\Export::firstTimeSuccessExport([
                            'EMAIL' => $arUser['EMAIL'],
                            'STORE_NAME' => \Local\Core\Inner\Store\Base::getStoreName($intStoreID),
                            'TP_NAME' => \Local\Core\Inner\TradingPlatform\Base::getName($intId),
                            'PRODUCTS_TOTAL' => number_format($arLog['PRODUCTS_TOTAL'], 0, '.', ' '),
                            'PRODUCTS_EXPORTED' => number_format($arLog['PRODUCTS_EXPORTED'], 0, '.', ' '),
                            'EXPORT_LINK' => \Local\Core\Inner\TradingPlatform\Base::getExportFileLink($intId),
                            'STORE_LINK' => Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => \Local\Core\Inner\Store\Base::getCompanyId($intStoreID), '#STORE_ID#' => $intStoreID]),
                        ]);
                    }
                }

                \Local\Core\Model\Data\TradingPlatformExportLogTable::add($arLog);

                if( ( !$obResult->isSuccess() || !empty( $arFilledCheckErrors ) )  && $boolSendMailIfError )
                {
                    $arFilledCheckErrors = array_merge($arFilledCheckErrors, $obResult->getErrorMessages());
                    $intStoreID = \Local\Core\Inner\TradingPlatform\Base::getStoreId($intId);
                    $arUser = \Bitrix\Main\UserTable::getByPrimary(\Local\Core\Inner\Store\Base::getOwnUserId($intStoreID), ['select' => ['EMAIL']])->fetch();

                    if( !empty( $arUser['EMAIL'] ) )
                    {
                        \Local\Core\Inner\TriggerMail\TradingPlatform\Export::errorDuringExport([
                            'EMAIL' => $arUser['EMAIL'],
                            'STORE_NAME' => \Local\Core\Inner\Store\Base::getStoreName($intStoreID),
                            'TP_NAME' => \Local\Core\Inner\TradingPlatform\Base::getName($intId),
                            'ERROR_TEXT' => implode('<br/>', $arFilledCheckErrors),
                            'STORE_ROUTE' => Route::getRouteTo('store', 'detail', ['#COMPANY_ID#' => \Local\Core\Inner\Store\Base::getCompanyId($intStoreID), '#STORE_ID#' => $intStoreID ])
                        ]);
                    }
                }
            }

        }

        return $obResult;
    }

    /**
     * Метод создает очередь экспортных файлов
     */
    public static function createQueue()
    {
        $intTimeoutBetweenInMin = \Bitrix\Main\Config\Configuration::getInstance()
                                             ->get('tradingplatform')['export']['timeout_between_export'] ?? 240;
        $intTimestamp = (new \Bitrix\Main\Type\DateTime())->add('-'.$intTimeoutBetweenInMin.' minutes')->getTimestamp();

        $rsTp = \Local\Core\Model\Data\TradingPlatformTable::getList([
            'filter' => ['ACTIVE' => 'Y'],
            'select' => ['ID']
        ]);
        while ($arTp = $rsTp->fetch())
        {
            $arLog = \Local\Core\Model\Data\TradingPlatformExportLogTable::getList([
                'filter' => ['TP_ID' => $arTp['ID']],
                'order' => ['DATE_CREATE' => 'DESC'],
                'limit' => 1,
                'select' => ['DATE_CREATE']
            ])->fetch();
            if(
                !($arLog['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime)
                || (
                        $arLog['DATE_CREATE'] instanceof \Bitrix\Main\Type\DateTime
                        && $arLog['DATE_CREATE']->getTimestamp() < $intTimestamp
                    )
            )
            {
                $worker = new \Local\Core\Inner\JobQueue\Worker\TradingPlatformExport(['TP_ID' => $arTp['ID']]);
                $dateTime = new \Bitrix\Main\Type\DateTime();
                \Local\Core\Inner\JobQueue\Job::addIfNotExist($worker, $dateTime, 1);
            }
        }
    }
}