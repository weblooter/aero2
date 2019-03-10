<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 29.12.18
 * Time: 14:02
 */

namespace Local\Core\Inner\Client\Dadata;


use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\Type\DateTime;
use Local\Core\Assistant\Arrays;
use Local\Core\Model\Data\DadataCacheTable;
use Psr\Container\ContainerInterface;

/**
 * Класс кеширования обращений к дадате
 * Class Cache
 * @package Local\Core\Inner\Client\Dadata
 */
class Cache
{
    /**
     * Время жизни кэша
     */
    const CACHE_TIME = 2678400;

    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    /**
     * Cache constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Получить кэш ответа сервиса дадата
     * @param Interfaces\QueryInterface $query
     * @return |null
     */
    public function get(Interfaces\QueryInterface $query)
    {
        $hash = $this->hash($query);

        $ar_filter = [
            'select' => [
                'ID',
                'DATA'
            ],
            'filter' => [
                'HASH' => $hash,
            ],
            'limit' => 1,
        ];

        $result = DadataCacheTable::getList($ar_filter);

        if ($row = $result->fetch()) {
            DadataCacheTable::hit($row['ID']);
            return $row['DATA'];
        }

        return null;
    }

    /**
     * Поместить в кэш ответ сервиса дадата
     * @param Interfaces\QueryInterface $query
     * @param array $response
     * @return bool
     */
    public function set(Interfaces\QueryInterface $query, array $response)
    {
        $hash = $this->hash($query);

        $ar_fields = [
            'TYPE' => 'GENERAL', // пока в запросе нет никакого идентификатора, поэтому все запросы пока будут одного типа, как появится необходимость разграничить - сделаем
            'HASH' => $hash,
            'DATA' => $response,
        ];

        /** @var $result AddResult */
        $result = DadataCacheTable::add($ar_fields);

        return $result->isSuccess();
    }

    /**
     * @return |null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectException
     */
    public function clear()
    {
        $date = new DateTime;
        $date->add('- ' . self::CACHE_TIME . ' second');

        $ar_filter = [
            'select' => [
                'ID'
            ],
            'filter' => [
                '<DATE_ADD' => $date,
            ],
        ];

        $result = DadataCacheTable::getList($ar_filter);

        while ($row = $result->fetch()) {
            DadataCacheTable::delete($row['ID']);
        }
    }

    /**
     * Получить хеш запроса к сервису дадата
     * @param Interfaces\QueryInterface $query
     * @return string
     */
    protected function hash(Interfaces\QueryInterface $query)
    {
        $dump = Arrays::dump($query->toArray());

        return md5($dump);
    }
}
