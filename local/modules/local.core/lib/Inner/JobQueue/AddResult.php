<?php
declare( strict_types = 1 );

namespace Local\Core\Inner\JobQueue;


class AddResult extends \Bitrix\Main\Result
{
    private $jobID;
    private $isAlreadyExist = false;

    /**
     * Возврашщает ID добавленной работы.
     * <br>Использовать только в случае успеха результата
     * @return mixed
     */
    public function getJobID()
    {
        return $this->jobID;
    }

    /**
     * @param mixed $jobID
     */
    public function setJobID(int $jobID): void
    {
        $this->jobID = $jobID;
    }

    /**
     * Была ли добавлена работа или уже существовала.
     * @return bool
     */
    public function isAlreadyExist(): bool
    {
        return $this->isAlreadyExist;
    }

    /**
     * @param bool $isAlreadyExist
     */
    public function setIsAlreadyExist(bool $isAlreadyExist): void
    {
        $this->isAlreadyExist = $isAlreadyExist;
    }
}
