<?php

namespace Local\Core\Inner\JobQueue\Worker\User;

use \Local\Core\Inner;
use \Bitrix\Main;

/**
 * Воркер синхронизации заказов пользователей со старым сайтом
 * Class OrderSync
 * @package Local\Core\Inner\JobQueue\Worker
 */
class ImportOrders extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * @return Main\Result
     * {@inheritdoc}
     * @package Local\Core\Inner\JobQueue\Worker
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();

        $user_id = (int)$arInputData[ 'USER_ID' ];

        if ( $user_id <= 0 )
        {
            throw new Inner\JobQueue\Exception\FailException( 'Не верный ID пользователя' );
        }

        try
        {
            \Local\Core\Exchange\Onec\Webservice\OrderSync::syncByUser( $user_id );
        }
        catch ( \Exception $e )
        {
            throw new \Exception( 'При синхронизации заказов пользователя [ID='.$user_id.'] возникло исключение: '.$e->getMessage() );
        }

        return $result;
    }

    public function getNextExecuteAt( int $addSecond = 120 ): Main\Type\DateTime
    {
        //Some Another logic
        return parent::getNextExecuteAt( $addSecond );
    }
}
