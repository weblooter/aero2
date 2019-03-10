<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 11.12.18
 * Time: 12:55
 */

namespace Local\Core\Inner\Client\Dadata;

/**
 * Класс обеспечивающий возможность расширенного поиска информации о юрлицах
 * Class LegalPartyClient
 * @package Local\Core\Inner\Client\Dadata
 */
class LegalPartyClient extends BaseClient
{
    /** @var string Часть урла, указывающая на сервис поиска информации по юрлицам по ИНН и ОГРН.*/
    protected $resource = '/findById/party';

    /**
     * Получить подсказку по юрлицу
     * @param Interfaces\QueryInterface $query
     * @return \Bitrix\Main\Result
     */
    public function suggest(Interfaces\QueryInterface $query) {

        return parent::suggest($this->resource, $query);
    }

    protected function signHash(&$response)
    {
        foreach($response['suggestions'] as &$suggestion)
        {
            $ar_data = [
                $suggestion['unrestricted_value'],
                $suggestion['data']['kpp'],
                $suggestion['data']['inn'],
                $suggestion['data']['management']['name'],
                $suggestion['data']['management']['post'],
                $suggestion['data']['address']['unrestricted_value'],
            ];

            $suggestion['hash'] = self::hash($ar_data);
        }
    }
}
