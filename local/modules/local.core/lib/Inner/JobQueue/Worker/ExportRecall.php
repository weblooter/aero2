<?php

namespace Local\Core\Inner\JobQueue\Worker;

use \Local\Core\Inner;
use \Bitrix\Main;

/**
 * Воркер для экспорта обратных звонков в 1С.<br>
 * Class ExportRecall
 * @package Local\Core\Inner\JobQueue\Worker
 */
class ExportRecall extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * @return Main\Result
     * {@inheritdoc}
     * @throws Inner\JobQueue\Exception\FailException
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();

        $webFormId = (int)$arInputData['WEB_FORM_ID'];
        $resultId = (int)$arInputData['RESULT_ID'];

        if ($webFormId <= 0) {
            throw new Inner\JobQueue\Exception\FailException('Не верный ID web формы');
        }

        if ($resultId <= 0) {
            throw new Inner\JobQueue\Exception\FailException('Не верный ID результата web формы');
        }

        try{
            \Local\Core\Exchange\Onec\ExportRecall::export($webFormId, $resultId);
        } catch (\Exception $e){
            throw new \Exception("При попытке выгрузки заявки на обратный звонок [WEB_FORM_ID = $webFormId, RESULT_ID = $resultId] в 1С возникло исключение: ".$e->getMessage());
        }

        return $result;
    }


}
