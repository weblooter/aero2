<?php

namespace Local\Core\Inner\JobQueue\Worker;

use \Local\Core\Inner;
use \Bitrix\Main;

/**
 * Воркер импорта робофида в систему
 *
 * {@inheritdoc}
 * @package Local\Core\Inner\JobQueue\Worker
 */
class StoreRobofeedImport extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * {@inheritdoc}
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();

        $rs = \Local\Core\Inner\Robofeed\ImportData::execute($arInputData['STORE_ID']);

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
