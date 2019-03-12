<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 11.12.18
 * Time: 12:55
 */

namespace Local\Core\Inner\Client\Dadata;

/**
 * Класс обеспечивающий возможность получения подсказок по адресам
 * Class AddressClient
 * @package Local\Core\Inner\Client\Dadata
 */
class AddressClient extends BaseClient
{
    /** @var string  Часть урла указывающая на сервис поиска информации по адресам */
    protected $resource = '/suggest/address';

    /**
     * Получить подсказку по адресу
     *
     * @param Interfaces\QueryInterface $query
     *
     * @return \Bitrix\Main\Result
     */
    public function suggest( Interfaces\QueryInterface $query )
    {

        return parent::suggest( $this->resource, $query );
    }
}

