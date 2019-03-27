<?php

namespace Local\Core\Inner\JobQueue;

use \Local\Core\Model;
use \Symfony\Component\Process\Process;


set_time_limit(0);

final class Runner
{
    const TIME_BETWEEN_CYCLES = 10;
    const MAX_CYCLES_COUNT = 5;
    const MAXIMUM_WORKERS = 2;
    private static $arProcess = [];

    public function __construct()
    {
        if( php_sapi_name() != 'cli' )
        {
            p('Only CLI');
            die;
        }
    }

    /**
     * Стартует раннер
     */
    public function start()
    {
        $this->startDaemonIfPossible();
    }


    /**
     * Стартует демона, если процесс никаден
     */
    private function startDaemonIfPossible()
    {
        $arRes = [];
        exec(
            'ps aux | grep "'.self::getProcessName().'" | grep -v grep',
            $arRes
        );
        if( empty($arRes) )
        {
            cli_set_process_title(self::getProcessName());
            $this->startDaemon();
        }
    }

    /**
     * Возвращает имя процесса
     * @return string
     */
    private static function getProcessName()
    {
        return 'robofeed_queue_job_runner';
    }

    /**
     * Возвращает насройки и текущий статус раннера
     *
     * @return array
     */
    public static function getRunnerStatus()
    {
        $arReturn = [
            'MAXIMUM_WORKERS' => \Bitrix\Main\Config\Configuration::getInstance()->get('job_queue')['MAXIMUM_WORKERS'] ?? self::MAXIMUM_WORKERS,
            'MAX_CYCLES_COUNT' => \Bitrix\Main\Config\Configuration::getInstance()->get('job_queue')['MAX_CYCLES_COUNT'] ?? self::MAX_CYCLES_COUNT,
            'TIME_BETWEEN_CYCLES' => \Bitrix\Main\Config\Configuration::getInstance()->get('job_queue')['TIME_BETWEEN_CYCLES'] ?? self::TIME_BETWEEN_CYCLES,
            'COMMAND' => 'ps aux | grep "'.self::getProcessName().'" | grep -v grep'
        ];
        $arReturn['STATUS'] = exec($arReturn['COMMAND']);
        return $arReturn;
    }

    /**
     * Стартует демона
     */
    private function startDaemon()
    {
        $counter = 0;
        while( true )
        {
            $MAX_CYCLES_COUNT = \Bitrix\Main\Config\Configuration::getInstance()->get('job_queue')['MAX_CYCLES_COUNT'] ?? self::MAX_CYCLES_COUNT;
            $TIME_BETWEEN_CYCLES = \Bitrix\Main\Config\Configuration::getInstance()->get('job_queue')['TIME_BETWEEN_CYCLES'] ?? self::TIME_BETWEEN_CYCLES;

            if( $MAX_CYCLES_COUNT > 0 )
            {
                $counter++;
                if( $counter >= $MAX_CYCLES_COUNT )
                {
                    die;
                }
            }

            $this->executeJobs();
            sleep($TIME_BETWEEN_CYCLES);
        }
    }

    /**
     * Очищает список процессов при их завершённости<br>
     * Получает список работ до лимита<br>
     * Инитит добавление форка.
     *
     */
    private function executeJobs()
    {

        foreach( self::$arProcess as $k => $process )
        {
            /** @var Process $process */
            if( !$process->isRunning() )
            {
                unset(self::$arProcess[$k]);
            }
        }

        if( empty(self::$arProcess) )
        {
            self::$arProcess = [];
        }

        $maximumJob = $this->getMaximumWorkers() - count(self::$arProcess);

        $arJob = $this->findJob((int)$maximumJob);
        foreach( $arJob as $ar )
        {
            $this->executeJobByWorkerProcess($ar);
        }

    }

    /**
     * Максимальное количество воркеров
     * @return int
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     */
    private function getMaximumWorkers(): int
    {
        return (int)\Bitrix\Main\Config\Configuration::getInstance()
                ->get('job_queue')['MAXIMUM_WORKERS'] ?? self::MAXIMUM_WORKERS;
    }

    /**
     * Ищет работу<br>
     * Больше нуля количество попыток<br>
     * Больше текущего время выполнения.<br>
     * Статус новый  или обшибка<br>
     * НЕ выполняемый сейчас<br>
     * Исполнитель равен никто<br>
     *
     * @param int $maximum
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function findJob(int $maximum): array
    {
        if( $maximum < 1 )
        {
            return [];
        }

        $ar = Model\Data\JobQueueTable::getList(
            [
                'filter' => [
                    '>ATTEMPTS_LEFT' => 0,
                    '<=EXECUTE_AT' => \Bitrix\Main\Type\DateTime::createFromTimestamp(time()),
                    '=STATUS' => [
                        Model\Data\JobQueueTable::STATUS_ENUM_NEW,
                        Model\Data\JobQueueTable::STATUS_ENUM_ERROR,
                    ],
                    '=IS_EXECUTE_NOW' => 'N',
                    '=EXECUTE_BY' => Model\Data\JobQueueTable::EXECUTE_BY_DEFAULT,
                ],
                'limit' => $maximum,
                'select' => ['ID'],
            ]
        )
            ->fetchAll();

        return is_array($ar) ? $ar : [];
    }

    /**
     * Запускаеи процесс воркера
     *
     * @param $ar
     *
     * @throws \Bitrix\Main\SystemException
     */
    private function executeJobByWorkerProcess($ar)
    {
        $executorID = uniqid(
            getmypid(),
            true
        );
        $updateData = [
            [
                'ID' => $ar['ID'],
                'EXECUTE_BY' => Model\Data\JobQueueTable::EXECUTE_BY_DEFAULT,
            ],
            [
                'EXECUTE_BY' => $executorID,
            ]
        ];

        $rs = Model\Data\JobQueueTable::update(
            ...
            $updateData
        );
        if( $rs->isSuccess() )
        {

            if( $rs->getAffectedRowsCount() === 1 )
            {
                try
                {
                    $rand = rand();
                    self::$arProcess[$rand] = new Process(
                        join(
                            ' ',
                            $this->getProcessConfig(
                                (int)$ar['ID'],
                                $executorID
                            )
                        )
                    );
                    self::$arProcess[$rand]->setTimeout(0);
                    self::$arProcess[$rand]->start();

                }
                catch( \Throwable $t )
                {
                    \Bitrix\Main\Application::getInstance()
                        ->getExceptionHandler()
                        ->writeToLog($t);

                }
            }
            else
            {
                #todo LogWriter
            }
        }
        else
        {
            #todo LogWriter
        }

    }

    /**
     * Возвращает массив для инита процесса
     *
     * @param int    $jobID
     * @param string $executorID
     *
     * @return array
     */
    private function getProcessConfig(int $jobID, string $executorID): array
    {
        $arReturn = [];
        $arReturn[] = "/usr/bin/php -d mbstring.func_overload=2";
        $arReturn[] = \Bitrix\Main\Application::getDocumentRoot()."/local/tools/console";
        $arReturn[] = "worker";
        $arReturn[] = (string)$jobID;
        $arReturn[] = $executorID;
        return $arReturn;
    }
}

