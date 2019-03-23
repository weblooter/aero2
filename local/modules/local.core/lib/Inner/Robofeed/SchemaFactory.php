<?php

namespace Local\Core\Inner\Robofeed;


class SchemaFactory
{
    /**
     * @param integer $intVersion ID версии
     *
     * @return Schema\Base
     */
    public static function factory($intVersion)
    {
        switch((string)$intVersion)
        {
            case '1':
                return new Schema\Base(1);
                break;
        }
    }
}