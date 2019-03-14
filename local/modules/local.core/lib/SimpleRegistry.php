<?php

namespace Local\Core;

/**
 * Класс регистра
 *
 * @package Local\Core
 */
class SimpleRegistry
{
    private $data = [];

    public function has($key)
    {
        return key_exists(
            $key,
            $this->data
        );
    }

    public function get($key)
    {
        if( strlen($key) > 0 )
        {
            return $this->data[$key];
        }

        return;
    }

    public function set($key, $val, $forse = false)
    {
        if( strlen($key) <= 0 )
        {
            throw new \Exception('Не указан ключ при установке значения!');
        }

        $has_key = $this->has($key);

        if( !$has_key || $has_key && $forse )
        {
            $this->data[$key] = $val;
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public static function init(array $config)
    {
        $registry = new self();

        foreach( $config as $key => $val )
        {
            $registry->set(
                $key,
                $val
            );
        }

        return $registry;
    }
}
