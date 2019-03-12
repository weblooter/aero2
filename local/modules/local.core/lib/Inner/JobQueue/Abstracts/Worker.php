<?php

namespace Local\Core\Inner\JobQueue\Abstracts;

use Bitrix\Main\ORM\Data\UpdateResult;
use Local\Core\Model\Data\JobQueueTable;
use Local\Core\Assistant;
use Local\Core\Inner;
use Bitrix\Main;

/**
 * Все воркеры работают из консоли.<br>
 * Штатно режим - cli.
 *
 * Class Worker
 * @package Local\Core\Inner\JobQueue\Abstracts
 * @uses  \Local\Core\Inner\JobQueue\Worker\Example
 */
abstract class Worker
{

    /**
     * @var array
     */
    private $inputData = [];
    /**
     * Входящий ID задачи
     */
    private $dirtyJobID;
    /**
     * Входящий ID исполнителя
     */
    private $dirtyExecutorID;
    /**
     * ID задачи
     */
    private $jobID;
    /**
     * Текущая работа
     * @var array
     */
    private $arCurrentJob = [];
    /**
     * Флаг возможности продолжать
     * @var bool
     */
    private $canContinue = true;

    final public function __construct( array $arData = [], $jobID = false, $executorID = false )
    {
        $this->inputData = $arData;
        $this->dirtyJobID = $jobID;
        $this->dirtyExecutorID = $executorID;
    }

    /**
     * Возвращает имя класса с учётом
     * @return string
     */
    final public static function getClassName(): string
    {
        return Assistant\Scalar\Strings::getAbsoluteClassName( static::class );
    }

    /**
     * Возвращает установленную дату
     * @return array
     */
    final public function getInputData(): array
    {
        return $this->inputData;
    }

    /**
     * Выполняет воркер
     * @throws \Throwable
     */
    final public function execute()
    {
        $this->getClearJobID();
        if ( !is_numeric( $this->getJobID() ) )
        {
            return; //TODO logWriter
        }

        try
        {
            $this->markJobStart();
            if ( !$this->isCanContinue() )
            {
                return;
            }
            $result = $this->doJob();

        }
        catch ( Inner\JobQueue\Exception\FailException $e )
        {
            $this->markJobFail();
            return;
        }
        catch ( \Throwable $t )
        {
            $this->markJobError();
            throw $t;
        }

        if ( $result instanceof \Bitrix\Main\Result )
        {
            if ( $result->isSuccess() )
            {
                $this->markJobSuccess();
            }
            else
            {
                $this->markJobError();
            }
        }
    }

    private function getClearJobID()
    {
        $ar = JobQueueTable::getList( [
            'filter' => [
                '=ID' => $this->dirtyJobID,
                '=EXECUTE_BY' => $this->dirtyExecutorID,
            ],
            'select' => ['*'],
        ] )->fetch();

        if ( !empty( $ar ) )
        {
            $this->setClearJobID( $ar[ 'ID' ] );
            $this->setCurrentJob( $ar );
        }
    }

    private function setClearJobID( $jobID )
    {
        $this->jobID = $jobID;

    }

    private function setCurrentJob( $ar )
    {
        $this->arCurrentJob = $ar;
    }

    /**
     * Возвращает проверенный jobID
     * @return mixed
     */
    private function getJobID()
    {
        return $this->jobID;
    }

    final private function markJobStart()
    {

        $updateData = [
            [
                'ID' => $this->getJobID(),
                'EXECUTE_BY' => $this->dirtyExecutorID,
            ],
            [
                'ATTEMPTS_LEFT' => new Main\DB\SqlExpression( '?# - 1', 'ATTEMPTS_LEFT' ),
                'IS_EXECUTE_NOW' => 'Y',
                'LAST_EXECUTE_START' => new Main\Type\DateTime(),
            ]
        ];

        $rs = JobQueueTable::update( ...$updateData );
        $this->checkUpdateResult( $rs, $updateData );
    }

    final private function markJobFail()
    {

        $updateData = [
            [
                'ID' => $this->getJobID(),
                'EXECUTE_BY' => $this->dirtyExecutorID,
            ],
            [
                'STATUS' => JobQueueTable::STATUS_ENUM_FAIL,
                'EXECUTE_BY' => JobQueueTable::EXECUTE_BY_DEFAULT,
                'IS_EXECUTE_NOW' => 'N',
            ]
        ];

        $rs = JobQueueTable::update( ...$updateData );
        $this->checkUpdateResult( $rs, $updateData );

    }

    final private function markJobError()
    {
        $updateData = [
            [
                'ID' => $this->getJobID(),
                'EXECUTE_BY' => $this->dirtyExecutorID,
            ],
            [
                'STATUS' => JobQueueTable::STATUS_ENUM_ERROR,
                'EXECUTE_BY' => JobQueueTable::EXECUTE_BY_DEFAULT,
                'IS_EXECUTE_NOW' => 'N',
                'EXECUTE_AT' => $this->getNextExecuteAt(),
            ]
        ];
        $rs = JobQueueTable::update( ...$updateData );
        $this->checkUpdateResult( $rs, $updateData );
    }

    final private function markJobSuccess()
    {

        $updateData = [
            [
                'ID' => $this->getJobID(),
                'EXECUTE_BY' => $this->dirtyExecutorID,
            ],
            [
                'STATUS' => JobQueueTable::STATUS_ENUM_SUCCESS,
                'EXECUTE_BY' => JobQueueTable::EXECUTE_BY_DEFAULT,
                'IS_EXECUTE_NOW' => 'N',
            ]
        ];

        $rs = JobQueueTable::update( ...$updateData );
        $this->checkUpdateResult( $rs, $updateData );

    }

    final private function checkUpdateResult( UpdateResult $result, $arData = [] )
    {
        if ( $result->isSuccess() )
        {
            if ( $result->getAffectedRowsCount() <> 1 )
            {
                $this->canContinue = false;
                #TODO logWriter
//                $this->getDefaultLogger()->addWarning('Job not update', ['TYPE' => 'WORKER', '__DATA' => $arData]);
            }
        }
        else
        {
            /** @var \Bitrix\Main\Error $error */
            foreach ( $result->getErrorCollection() as $error )
            {
                $this->canContinue = false;
                #TODO logWriter
//                $this->getDefaultLogger()->addWarning($error->getMessage(), [
//                    'TYPE' => 'WORKER',
//                    '__BX_CODE' => $error->getCode(),
//                    '__DATA' => $arData,
//                    '__BX_CUSTOM_DATA' => $error->getCustomData(),
//                ]);
            }
        }
    }

    /**
     * @param mixed $dirtyExecutorID
     *
     * @return Worker
     */
    public function setDirtyExecutorID( $dirtyExecutorID )
    {
        $this->dirtyExecutorID = $dirtyExecutorID;
        return $this;
    }

    /**
     * @return \Monolog\Logger
     * @throws \Exception
     */
    final private function getDefaultLogger()
    {
        return $logger = Inner\ContainerDI::getInstance()->get( 'logger' );
    }

    /**
     * <hr>
     * Здесь должна быть реализация Воркера.
     * Все \Exception, \Error, и НЕуспешные Bitrix\Main\Result<br>
     * будут перехвачены и преведут к перезапуску job, если остались попытки.<br>
     * А также залогированны<br><br>
     * <b>Важно</b>
     * Если требуется принудительно прекратить job<br>
     * <code>
     * throw new \Local\Core\Inner\JobQueue\Exception\Fail
     * </code>
     * @return \Bitrix\Main\Result
     * @throws \Local\Core\Inner\JobQueue\Exception\FailException
     */
    abstract public function doJob(): Main\Result;


    /**
     * Вовзращает время следующего запуска в случае ошибки().<br>
     * Можно переопределить.
     *
     * @param int $addSecond
     *
     * @return \Bitrix\Main\Type\DateTime
     * @throws \Bitrix\Main\ObjectException
     */
    public function getNextExecuteAt( int $addSecond = 120 ): Main\Type\DateTime
    {
        $dateTime = new Main\Type\DateTime();
        return $dateTime->add( '+ '.$addSecond.' sec' );
    }

    /**
     * Получает текущую работу, как запись из БД
     * @return array
     */
    final public function getCurrentJob()
    {
        return $this->arCurrentJob;
    }

    final private function isCanContinue()
    {
        return $this->canContinue;
    }


}
