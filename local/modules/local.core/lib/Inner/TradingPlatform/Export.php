<?php


namespace Local\Core\Inner\TradingPlatform;

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
     *
     * @return \Bitrix\Main\Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function execute($intId)
    {
        $obResult = new \Bitrix\Main\Result();

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
                    'STORE_ID' => \Local\Core\Inner\TradingPlatform\Base::getStoreIdByTpId($intId),
                    'TP_ID' => $intId,
                    'RESULT' => $obResult->isSuccess() ? 'SU' : 'ER',
                    'PRODUCTS_TOTAL' => !empty( $obResult->getData()['PRODUCTS_TOTAL'] ) ? $obResult->getData()['PRODUCTS_TOTAL'] : 0,
                    'PRODUCTS_EXPORTED' => !empty( $obResult->getData()['PRODUCTS_EXPORTED'] ) ? $obResult->getData()['PRODUCTS_EXPORTED'] : 0
                ];
                if( !$obResult->isSuccess() )
                {
                    $arLog['ERROR_TEXT'] = implode('<br/>', $obResult->getErrorMessages());
                }

                \Local\Core\Model\Data\TradingPlatformExportLogTable::add($arLog);
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