<?php

namespace Local\Core\Inner\Robofeed\Schema;


class Factory
{
    /**
     * @param integer $intVersion ID версии
     *
     * @return \Local\Core\Inner\Robofeed\Schema\AbstractSchema
     *
     * @throws \Local\Core\Inner\Exception\FatalException
     */
    public static function factory($intVersion)
    {
        switch ((string)$intVersion) {
            case '1':
                return new \Local\Core\Inner\Robofeed\Schema\V1\Schema();
                break;
        }
    }
}