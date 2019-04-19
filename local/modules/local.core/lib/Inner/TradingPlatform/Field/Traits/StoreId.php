<?php

namespace Local\Core\Inner\TradingPlatform\Field\Traits;


trait StoreId
{
    /** @var integer $intStoreId ID магазина */
    protected $intStoreId;

    /**
     * Задать ID магазина
     *
     * @param $intStoreId
     *
     * @return $this
     */
    public function setStoreId($intStoreId)
    {
        $this->intStoreId = $intStoreId;
        return $this;
    }

    /**
     * Получить ID магазина
     *
     * @return int
     */
    protected function getStoreId()
    {
        return $this->intStoreId;
    }
}