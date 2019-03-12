<?php

namespace Local\Core\Inner\JobQueue\Worker\User;

use \Local\Core\Inner;
use \Bitrix\Main;
use Local\Core\Model;

/**
 * Импорт Юр.Лиц по XML_ID пользователя
 * Class OrganizationImport
 *
 * {@inheritdoc}
 * @package Local\Core\Inner\JobQueue\Worker
 */
class ImportOrganizations extends Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * {@inheritdoc}
     */
    public function doJob(): Main\Result
    {
        $result = new Main\Result();
        $arInputData = $this->getInputData();
        $userXMLID = $arInputData[ 'XML_ID' ];

        $arJob = $this->getCurrentJob();
        $jobID = $arJob[ 'ID' ];

        if ( !is_numeric( $userXMLID ) )
        {
            throw new Inner\JobQueue\Exception\FailException( "userXMLID не число. jobID = {$jobID}" );
        }

        $arUser = Main\UserTable::getList( [
            'filter' => ['=XML_ID' => $userXMLID],
            'select' => ['ID'],
            'limit' => 1,
        ] )->fetch();

        if ( !is_numeric( $arUser[ 'ID' ] ) )
        {
            throw new Inner\JobQueue\Exception\FailException( "не найден user по XML_ID. jobID = {$jobID}" );
        }
        $userID = $arUser[ 'ID' ];

        $connection = \Local\Core\Assistant\Main\Connection::getNetCatConnection();
        $prepareID = $connection->getSqlHelper()->forSql( $userXMLID );
        $rs = $connection->query( "SELECT `ID1C` FROM `Message1161` where `User_ID` = $prepareID;" );
        while ( $ar = $rs->fetch() )
        {
            $arID[] = $ar[ 'ID1C' ];
        }

        if ( !is_array( $arID ) )
        {
            return $result;
        }


        $arID = array_filter( array_unique( array_map( 'trim', $arID ) ) );

        if ( empty( $arID ) )
        {
            return $result;
        }

        foreach ( $arID as $xmlID )
        {
            $soapClient = \Local\Core\Exchange\Onec\Webservice\Soap::getInstance();

            $rs = $soapClient->GetUrLico(
                [
                    "GetUrLicoStruct" => [
                        'id' => $userXMLID,
                        'UrLicoKod' => $xmlID,
                    ]
                ]
            );
            if ( $rs->isSuccess() )
            {
                $arResponse = $rs->getData();
                $ar = (array)$arResponse[ 'RESPONSE' ]->return;

                if ( $ar[ 'id' ] <> $userXMLID )
                {
                    $result->addError( new Main\Error( "Response id = {$ar['id']} не соответствует запрашиваему {$userXMLID}",
                        0, $arResponse ) );
                    #todo logWriter
                    return $result;
                }

                $arData = [
                    'ACTIVE' => 'Y',
                    'APPROVED' => Model\Reference\OrganizationTable::APPROVED_YES,
                    'NAME' => $ar[ 'NameCompany' ],
                    'OPF' => $ar[ 'OPF' ],
                    'INN' => $ar[ 'INNCompany' ],
                    'KPP' => $ar[ 'KPPCompany' ],
                    'LEGAL_ADDRESS' => $ar[ 'UrAdress' ],
                    'POST_ADDRESS' => $ar[ 'PochAdress' ],
                    'BANK' => $ar[ 'Bank' ],
                    'BIK' => $ar[ 'BIK' ],
                    'RSCH' => $ar[ 'Rasschet' ],
                    'FAX' => $ar[ 'FaxCompany' ],
                    'USER_ID' => $userID,
                    'XML_ID' => $xmlID,
                ];

                $ar = Model\Reference\OrganizationTable::getList( [
                    'filter' => ['XML_ID' => $xmlID],
                    'select' => ['ID'],
                ] )->fetch();

                if ( !empty( $ar ) )
                {
                    $arData = [$ar[ 'ID' ], $arData];
                    #todo check need update
                    $rs = Model\Reference\OrganizationTable::update( ...$arData );

                }
                else
                {
                    $rs = Model\Reference\OrganizationTable::add( $arData );
                }

                if ( $rs->isSuccess() )
                {
                    if ( $rs instanceof Main\ORM\Data\UpdateResult )
                    {
                        if ( $rs->getAffectedRowsCount() !== 1 )
                        {
                            $rs->addError( new Main\Error( 'Обновлвена не 1 строка в организации' ) );
                            #todo logWriter
                        }
                    }
                }
                else
                {
                    $result->addErrors( $rs->getErrors() );
                }

            }
            else
            {
                #todo logWriter
            }
        }

        return $result;
    }
}
