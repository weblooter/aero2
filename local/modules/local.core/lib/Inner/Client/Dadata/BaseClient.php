<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 11.12.18
 * Time: 12:39
 */

namespace Local\Core\Inner\Client\Dadata;

use Bitrix\Main\Error;
use Bitrix\Main\Result;


/**
 * Класс базового клиента сервиса dadata.ru
 * Class BaseClient
 * @package Local\Core\Inner\Client\Dadata
 */
abstract class BaseClient
{
    /** @var string */
    protected $url;

    /** @var string Токен, смотри .settings.php */
    protected $token;

    /**
     * BaseClient constructor.
     */
    public function __construct()
    {


        $config = \Bitrix\Main\Config\Configuration::getInstance()->get( 'dadata' );

        $this->token = $config[ 'token' ];

        $this->url = $config[ 'url' ];
    }

    /**
     * Проверить HTTP-статус ответа сервиса
     *
     * @param array $headers
     *
     * @return array Массив, где ключ status - HTTP-статус ответа, message - сообщение об ошибке, если таковое имеется
     */
    protected function checkResponseStatus( array $headers )
    {
        $status_line = $headers[ 0 ];

        preg_match( '{HTTP\/\S*\s(\d{3})}i', $status_line, $match );

        $status = $match[ 1 ];

        $result = [
            'status' => $status,
            'message' => ''
        ];

        if ( $status >= 400 )
        {
            $result[ 'message' ] = 'Неправилный запрос';
        }

        if ( $status >= 500 )
        {
            $result[ 'message' ] = 'Произошла внутренняя ошибка сервиса во время обработки';
        }

        switch ( $status )
        {
            case '200':
                $result[ 'message' ] = 'Запрос успешно обработан';
                break;
            case '400':
                $result[ 'message' ] = 'Некорректный запрос (невалидный JSON или XML)';
                break;
            case '401':
                $result[ 'message' ] = 'В запросе отсутствует API-ключ';
                break;
            case '403':
                $result[ 'message' ] = 'В запросе указан несуществующий API-ключ Или не подтверждена почта или исчерпан дневной лимит по количеству запросов';
                break;
            case '404':
                $result[ 'message' ] = 'Запрос к несуществующему ресурсу';
                break;
            case '405':
                $result[ 'message' ] = 'Запрос сделан с методом, отличным от POST';
                break;
            case '413':
                $result[ 'message' ] = 'Слишком большая длина запроса или слишком много условий';
                break;
            case '429':
                $result[ 'message' ] = 'Слишком много запросов в секунду';
                break;
        }

        return $result;
    }

    /**
     * Получить подсказку с сервиса dadata.ru
     *
     * @param string                    $resource Конечная часть урла, указывающая на конкретный сервис подсказок
     * @param Interfaces\QueryInterface $query Объект запроса
     *
     * @return Result
     */
    public function suggest( string $resource, Interfaces\QueryInterface $query )
    {
        $result = new Result;

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-type: application/json',
                    'Authorization: Token '.$this->token,
                ),
                'content' => json_encode( $query->toArray() ),
            ),
        );
        $context = stream_context_create( $options );

        $response = file_get_contents( $this->url.$resource, false, $context );

        $response_status = $this->checkResponseStatus( $http_response_header );

        if ( $response_status[ 'status' ] == 200 )
        {
            $response = json_decode( $response, true );
            $response[ 'count' ] = 0;

            if ( key_exists( 'suggestions', $response ) && $response[ 'suggestions' ] > 0 )
            {
                $response[ 'count' ] = count( $response[ 'suggestions' ] );
            }

            $result->setData( $response );
        }
        else
        {
            $result->addError(
                new Error( $response_status[ 'message' ], $response_status[ 'status' ] )
            );
        }

        return $result;
    }
}
