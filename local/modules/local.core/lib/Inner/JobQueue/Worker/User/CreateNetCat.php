<?php

namespace Local\Core\Inner\JobQueue\Worker\User;

use \Bitrix\Main;
use \Local\Core\Inner;
use \Local\Core\Exchange;

/**
 * Обновление пользователя в базе неткат
 * {@inheritdoc}
 * @package Local\Core\Inner\JobQueue\Worker
 */
class CreateNetCat extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * {@inheritdoc}
     */
    public function doJob(): Main\Result
    {
        $arInputData = $this->getInputData();
        $userId = (int)$arInputData[ "USER_ID" ];
        $arJob = $this->getCurrentJob();

        if ( empty( $userId ) )
        {
            throw new Inner\JobQueue\Exception\FailException( "USER_ID пустое. Job-{$arJob['ID']}" );
        }

        $resultAdd = \Local\Core\Business\User\NetCatUser::doRegister( $userId );
        if ( !$resultAdd->isSuccess() )
        {
            /** @var $obError \Bitrix\Main\Error */
            foreach ( $resultAdd->getErrors() as $obError )
            {
                if ( $obError->getCode() == "FATAL" )
                {
                    throw new Inner\JobQueue\Exception\FailException( $obError->getMessage() );
                }
            }
        }

        return $resultAdd;
    }
}
