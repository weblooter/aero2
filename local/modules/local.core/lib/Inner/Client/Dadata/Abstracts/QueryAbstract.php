<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 28.12.18
 * Time: 11:19
 */

namespace Local\Core\Inner\Client\Dadata\Abstracts;


use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Local\Core\Inner\Client\Dadata\Exception\ArgumentException;

/**
 * Абстрактый класс запароса к сервису Dadata
 * Class QueryAbstract
 * @package Local\Core\Inner\Client\Dadata\Abstracts
 */
abstract class QueryAbstract
{
    /** @var array Возможные параметры запроса */
    protected $validationRules;

    /** @var array Параметры запроса */
    protected $params = [];

    public static function createFromArray( array $ar_query )
    {
        $instance = new static;

        foreach ( $ar_query as $key => $value )
        {
            $instance->set( $key, $value );
        }

        return $instance;
    }

    /**
     * QueryAbstract constructor.
     */
    public function __construct()
    {
        $this->setValidationRules();
    }

    /**
     * Установить набор правил валидации параметров запроса. Необходима реализация в потомке.
     * @return mixed
     */
    abstract protected function setValidationRules();

    /**
     * Установить параметр запроса
     *
     * @param string $name Имя параметра
     * @param        $value Значение параметра
     *
     * @return $this
     * @throws ArgumentException
     */
    public function set( string $name, $value )
    {
        $check_result = $this->checkParam( $name, $value );

        if ( !$check_result->isSuccess() )
        {
            $errors = implode( "\n", $check_result->getErrorMessages() );
            throw new ArgumentException( 'Некорректный параметр запроса: '.$errors );
        }

        $this->params[ $name ] = $value;

        return $this;
    }

    /**
     * Получить значение параметра
     *
     * @param string $name Имя параметра
     *
     * @return mixed
     */
    public function get( string $name )
    {
        return $this->params[ trim( $name ) ];
    }

    /**
     * Удалить параметр запроса
     *
     * @param string $name
     */
    public function unset( string $name )
    {
        unset( $this->params[ trim( $name ) ] );
    }

    /**
     * Валидировать параметра запроса. Вызывается в методе set.
     * @see QueryAbstract::set
     *
     * @param string $name Имя параметра, передается по ссылке
     * @param        $value Значение параметра, передается по ссылке
     *
     * @return Result
     */
    public function checkParam( string &$name, &$value )
    {
        $result = new Result;

        $name = trim( $name );

        if ( !key_exists( $name, $this->validationRules ) )
        {
            $result->addError( new Error( 'Неизвестный параметр запроса "'.$name.'"' ) );
        }

        if ( $result->isSuccess() && !call_user_func( $this->validationRules[ $name ], $value ) )
        {
            $result->addError( new Error( 'Недопустимое значение параметра "'.$name.'"' ) );
        }

        return $result;
    }

    /**
     * Получить запрос в виде массива
     * @return array
     */
    public function toArray(): array
    {
        return $this->params;
    }

    /**
     * Привести к строке. Магический метод.
     * @return string
     */
    public function __toString(): string
    {
        return json_encode( $this->toArray(), JSON_UNESCAPED_UNICODE );
    }
}