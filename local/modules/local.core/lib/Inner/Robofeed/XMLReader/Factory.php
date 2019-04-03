<?php

namespace Local\Core\Inner\Robofeed\XMLReader;


class Factory
{
    /**
     * @param integer $intVersion ID версии
     *
     * @return \Local\Core\Inner\Robofeed\XMLReader\AbstractXMLReader
     *
     * @throws \Local\Core\Inner\Exception\FatalException
     */
    public static function factory($intVersion)
    {
        switch ((string)$intVersion) {
            case '1':
                return new \Local\Core\Inner\Robofeed\XMLReader\V1\XMLReader();
                break;
        }
    }
}