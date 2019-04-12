<?php

namespace Local\Core\Inner\TradingPlatform;

/**
 * Базовый класс для работы с торговыми площадками
 *
 * @package Local\Core\Inner\TradingPlatform
 */
class Base
{
    /** @var array $_arTpData Данные fetch от ORM ТП */
    protected $_arTpData = [];

    /**
     * Загрузить данные ТП по ID
     *
     * @param $intID
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws Exceptions\TradingPlatformNotFoundException
     */
    public function load($intID)
    {
        $this->_arTpData = \Local\Core\Model\Data\TradingPlatformTable::getByPrimary($intID)
            ->fetch();
        if (empty($this->_arTpData)) {
            throw new Exceptions\TradingPlatformNotFoundException();
        }
    }

    /**
     * Получить обработчик ТП либо из загруженных ранее данных через ::load(), либо из значения параметра
     *
     * @param string $strHandler Символьный код обработчика. Если ТП ранее был загружен, то нет нужны указывать
     *
     * @return Handler\AbstractHandler
     * @throws Exceptions\HandlerNotFoundException
     */
    public function getHandler($strHandler = '')
    {
        $obHandler = null;
        if( !empty( $this->_arTpData['HANDLER'] ) )
        {
            $obHandler = \Local\Core\Inner\TradingPlatform\Factory::factory($this->_arTpData['HANDLER']);
            $obHandler->fillTradingPlatformSettings($this->_arTpData);
        }
        elseif( !empty( $strHandler ) )
        {
            $obHandler = \Local\Core\Inner\TradingPlatform\Factory::factory($strHandler);
        }

        return $obHandler;
    }
}