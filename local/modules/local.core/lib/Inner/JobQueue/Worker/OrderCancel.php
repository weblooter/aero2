<?php

namespace Local\Core\Inner\JobQueue\Worker;

use \Local\Core\Inner;
use \Bitrix\Main;

/**
 * Воркер для отмены заказа в 1С при отмене на сайте
 *
 * @package Local\Core\Inner\JobQueue\Worker
 */
class OrderCancel extends \Local\Core\Inner\JobQueue\Abstracts\Worker implements \Local\Core\Inner\Interfaces\UseInDb
{

    /**
     * {@inheritdoc}
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();

        \Bitrix\Main\Loader::includeModule( 'sale' );

        if ( empty( $arInputData[ 'ORDER_ID' ] ) )
        {
            throw new \Local\Core\Inner\JobQueue\Exception\FailException( 'Order id is empty' );
        }

        $obOrder = \Bitrix\Sale\Order::load( $arInputData[ 'ORDER_ID' ] );

        $obClient = \Local\Core\Exchange\Onec\Webservice\Soap::getInstance();
        $obRes = $obClient->CanselOrder( ['Orders' => $obOrder->getField( 'ACCOUNT_NUMBER' )] );

        /**
         * @var \Bitrix\Main\Result $obRes
         */
        if ( !$obRes->isSuccess() )
        {
            throw new \Exception( implode( '; ', $obRes->getErrorMessages() ) );
        }

        //Some php code

        //You can
        //throw New \Exception('Some');
        //Or
        //$result->addError(new \Bitrix\Main\Error('Some Error'));

        //If need final, without success
        //throw new \Local\Core\Inner\JobQueue\Exception\FailException('Финалочка');

        //All other throw \Throwable will be logged as critical
        //All \Bitrix\Main\Error in Result will NOT be logged. It's your issue

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getNextExecuteAt( int $addSecond = 120 ): Main\Type\DateTime
    {
        //Some Another logic
        return parent::getNextExecuteAt( $addSecond );
    }
}