<?php

namespace Local\Core\Inner\JobQueue\Worker;

use \Local\Core\Inner;
use \Bitrix\Main;

/**
 * Пример реализации воркера.<br>
 * Class Worker
 *
 * {@inheritdoc}
 * @package Local\Core\Inner\JobQueue\Worker
 */
class Example extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * {@inheritdoc}
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();

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
    public function getNextExecuteAt(int $addSecond = 120): Main\Type\DateTime
    {
        //Some Another logic
        return parent::getNextExecuteAt($addSecond);
    }

}
