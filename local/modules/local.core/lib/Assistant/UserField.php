<?php

namespace Local\Core\Assistant;

/**
 * Помошник для работы с пользовательскими свойствами
 * Class UserField
 * @package Local\Core\Assistant
 */
class UserField
{
    /**
     * @return \CUserTypeManager
     */
    protected static function getUserFieldManager()
    {
        return $GLOBALS["USER_FIELD_MANAGER"];
    }

    /**
     * Записать значение пользовательского свойства
     *
     * @param string $entity_id Имя объекта
     * @param string $value_id  Идентификатор элемента
     * @param string $uf_id     Имя пользовательского свойства
     * @param string $uf_value  Значение, которое сохраняем
     *
     * @return mixed
     */
    public static function set($entity_id, $value_id, $uf_id, $uf_value)
    {
        return self::getUserFieldManager()->Update($entity_id, $value_id, [$uf_id => $uf_value]);
    }

    /**
     * Получить значение пользовательского свойства
     *
     * @param string $entity_id Имя объекта
     * @param string $value_id  Идентификатор элемента
     * @param string $uf_id     Имя пользовательского свойства
     *
     * @return mixed
     */
    public static function get($entity_id, $value_id, $uf_id)
    {
        $arUF = self::getUserFieldManager()->GetUserFields($entity_id, $value_id);
        return $arUF[$uf_id]["VALUE"];
    }

    /**
     * @param string $entity_id Имя объекта
     * @param string $value_id  Идентификатор элемента
     *
     * @return mixed
     */
    public static function getList($entity_id, $value_id)
    {
        return self::getUserFieldManager()->GetUserFields($entity_id, $value_id);
    }

    /**
     * Возвращает значения
     *
     * @param string $entity_id Имя объекта
     * @param string $value_id  Идентификатор элемента
     *
     * @return array
     */
    public static function getListValues($entity_id, $value_id)
    {
        $ar = static::getList($entity_id, $value_id);
        foreach( $ar as &$v )
        {
            $v = $v['VALUE'];
        }
        unset($v);
        return $ar;
    }
}
