<?php

namespace Local\Core\Inner\JobQueue;

use Local\Core\Model\Data\JobQueueTable;

class Job
{

    /**
     * Добавляет работу воркера
     * <code>
     * $worker = new \Local\Core\Inner\JobQueue\Worker\Example(['key1'=>1111,222,333,444]);
     * $dateTime = new \Bitrix\Main\Type\DateTime();
     * $dateTime->add('+ 3600 sec');
     * $rs = \Local\Core\Inner\JobQueue\Add::job($worker, $dateTime, 2);
     * </code>
     *
     * @param \Local\Core\Inner\JobQueue\Abstracts\Worker $worker
     * @param \Bitrix\Main\Type\DateTime                  $executeAt
     * @param int                                         $attempts Число попыток
     *
     * @return \Local\Core\Inner\JobQueue\AddResult
     * @throws \Exception
     * @see \Local\Core\Inner\JobQueue\Worker\Example
     */
    public static function add(
        \Local\Core\Inner\JobQueue\Abstracts\Worker $worker, \Bitrix\Main\Type\DateTime $executeAt, int $attempts = 10
    ){
        $result = new AddResult();
        $addData = [
            'WORKER_CLASS_NAME' => $worker::getClassName(),
            'INPUT_DATA' => $worker->getInputData(),
            'EXECUTE_AT' => $executeAt,
            'ATTEMPTS_LEFT' => $attempts
        ];
        $rs = \Local\Core\Model\Data\JobQueueTable::add($addData);
        if( $rs->isSuccess() )
        {
            $result->setJobID($rs->getId());
            $addData['ID'] = $rs->getId();
            $result->setData(['jobData' => $addData]);
        }
        else
        {
            \Local\Core\Assistant\Throwable::addError($result, $rs->getErrorCollection());
        }
        return $result;
    }

    /**
     * Добавить задание, если такого задания еще не стоит
     * @see \Local\Core\Inner\JobQueue\Job::add
     *
     * @param Abstracts\Worker           $worker
     * @param \Bitrix\Main\Type\DateTime $executeAt
     * @param int                        $attempts
     *
     * @return \Local\Core\Inner\JobQueue\AddResult
     * @throws \Local\Core\Inner\Client\Dadata\Exception\ArgumentException
     */
    public static function addIfNotExist(
        \Local\Core\Inner\JobQueue\Abstracts\Worker $worker, \Bitrix\Main\Type\DateTime $executeAt, int $attempts = 10
    ){
        $result = new AddResult();
        $class = $worker::getClassName();
        $input = $worker->getInputData();
        $hash = JobQueueTable::hash($class, $input);

        /** @var $rows \Bitrix\Main\ORM\Query\Result */
        $rows = JobQueueTable::getList([
            'select' => [
                'ID',
                'WORKER_CLASS_NAME',
                'INPUT_DATA',
                'EXECUTE_AT',
                'ATTEMPTS_LEFT',
            ],
            'filter' => [
                'HASH' => $hash,
                '>ATTEMPTS_LEFT' => 0,
                'STATUS' => ['N', 'E']
            ],
            'limit' => 1,
        ]);

        $findJob = $rows->fetch();

        if( is_array($findJob) )
        {
            $result->setJobID($findJob['ID']);
            $result->setData(['jobData' => $findJob]);
            $result->setIsAlreadyExist(true);
        }
        else
        {
            return self::add(...func_get_args());
        }
        return $result;
    }
}

