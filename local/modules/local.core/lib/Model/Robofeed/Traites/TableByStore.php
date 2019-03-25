<?php

namespace Local\Core\Model\Robofeed\Traites;


trait TableByStore
{
    protected static $intStoreId;

    /**
     * @param $intStoreId
     *
     * @return $this
     */
    public function setStoreId($intStoreId)
    {
        self::$intStoreId = $intStoreId;
        return $this;
    }

    public function getStoreId()
    {
        return self::$intStoreId;
    }
}