<?php

namespace Local\Core\Inner\Robofeed\Validator;


class Factory
{
    /**
     * @param integer $intVersion ID версии
     *
     * @return \Local\Core\Inner\Robofeed\Validator\AbstractValidator
     *
     * @throws \Local\Core\Inner\Exception\FatalException
     */
    public static function factory($intVersion)
    {
        switch ((string)$intVersion) {
            case '1':
                return new \Local\Core\Inner\Robofeed\Validator\V1\Validator();
                break;
        }
    }
}