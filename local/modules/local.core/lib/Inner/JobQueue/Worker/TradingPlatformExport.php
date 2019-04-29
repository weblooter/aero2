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
class TradingPlatformExport extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * {@inheritdoc}
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();

        \Local\Core\Inner\TradingPlatform\Export::execute($arInputData['TP_ID']);

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
