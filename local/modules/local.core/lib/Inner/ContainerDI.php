<?php

namespace Local\Core\Inner;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ContainerDI
 * @deprecated
 *
 * @package Local\Core\Inner
 * @link    https://symfony.com/doc/current/components/dependency_injection.html
 */
class ContainerDI
{
    protected static $container = null;

    protected static $parameterBag = null;

    public static function getInstance()
    {
        if( is_null(self::$container) )
        {
            self::$container = new ContainerBuilder(self::$parameterBag);
        }

        return self::$container;
    }

    public static function setParameterBag(ParameterBagInterface $parameterBag = null)
    {
        self::$parameterBag = $parameterBag;
    }

    /**
     * Коструктор закрываем.
     */
    private function __construct()
    {

    }

    /**
     * Клонирование ззапрещаем
     */
    private function __clone()
    {

    }
}