<?php

namespace Local\Core\Inner\JobQueue\Worker;

use \Local\Core\Inner;
use \Bitrix\Main;

/**
 * Воркер экспорта юридических лиц пользователя
 * Class ExportOrganizations
 * @package Local\Core\Inner\JobQueue\Worker
 */
class ExportOrganizations extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
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

        $legal_id = (int)$arInputData[ 'LEGAL_ID' ];

        if ( $legal_id <= 0 )
        {
            throw new Inner\JobQueue\Exception\FailException( 'Не верный ID юридического лица' );
        }

        try
        {
            \Local\Core\Exchange\Onec\User\ExportOrganizations::export( $legal_id );
        }
        catch ( \Exception $e )
        {
            throw new \Exception( 'При попытке выгрузки данных юридического лица [ID='.$legal_id.'] в 1С возникло исключение: '.$e->getMessage() );
        }

        return $result;
    }
}
