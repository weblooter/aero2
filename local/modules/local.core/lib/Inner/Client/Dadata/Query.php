<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 11.12.18
 * Time: 12:57
 */

namespace Local\Core\Inner\Client\Dadata;

/**
 * Класс описывающий запрос к сервису dadata.ru
 * Class Query
 * @package Local\Core\Inner\Client\Dadata
 */
class Query extends Abstracts\QueryAbstract implements Interfaces\QueryInterface
{
    /**
     * @link https://dadata.ru/api/suggest/#restrictions
     * @link https://dadata.ru/api/suggest/#how-off-geolocation-address
     * @link https://dadata.userecho.com/knowledge-bases/4/articles/2090-kak-sdelat-vsyo-chto-ugodno-v-podskazkah
     */
    protected function setValidationRules()
    {
        $this->validationRules = [
            'query' => function (&$v)
                {
                    $v = trim($v);
                    $len = strlen($v);
                    return 0 < $len && $len <= 300;
                },
            'count' => function (&$v)
                {
                    $v = (int)$v;
                    return is_int($v) && $v > 0;
                },
            'type' => function ($v)
                {
                    return in_array($v, ['LEGAL', 'INDIVIDUAL']); // юрлица или ип
                },
            'branch_type' => function ($v)
                {
                    return in_array($v, ['MAIN']); // если нужна головная организация
                },

            /** На счет параметров ниже мне еще не все известно и тут они далеко не все, поэтому в качестве валидаторов пока только заглушки.
             * Для того добавить какую-то новую возможность следует поступить так:
             * - открыть страницу https://dadata.ru/api/suggest/#how-off-geolocation-address или https://dadata.userecho.com/knowledge-bases/4/articles/2090-kak-sdelat-vsyo-chto-ugodno-v-podskazkah
             * - найти подходящий пример
             * - выполнить его с включеной консолью браузера
             * - найти http-запрос который был инициирован выбранным примером и исследовать его
             * - добавить необходимые параметры в правила валидации
             *
             * После этого вы можете использовать новые параметры в своих запросах, например так:
             * <code>
             * $query->set('locations_boost', ['kladr_id' => '63000001']);
             * </code>
             */
            'locations' => function ($v)
                {
                    return true;
                },
            'locations_boost' => function ($v)
                {
                    return true;
                },
            'to_bound' => function ($v)
                {
                    return true;
                },
            'from_bound' => function ($v)
                {
                    return true;
                },
        ];
    }
}