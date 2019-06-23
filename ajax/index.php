<?php
require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php" );

use \Bitrix\Main\
{HttpApplication,
    HttpContext,
    Server,
    HttpRequest
};

use \Symfony\Component\
{Routing\RequestContext,
    Routing\RouteCollection,
    Routing\Route,
    HttpFoundation\Request,
    Routing\Matcher\UrlMatcher,
    Routing\Exception\ResourceNotFoundException,
    Routing\Exception\MethodNotAllowedException};

/**
 * Т.к. классы HttpRequest и HttpResponse у нас переопределены и расширены дополнительными методами, то вместо такой конструкции:
 * $request = \Bitrix\Main\Context::getCurrent()->getRequest();
 * $response = \Bitrix\Main\Context::getCurrent()->getResponse();
 *
 * будем сами создавать объекты request'а и respons'а:
 */
$app = HttpApplication::getInstance();

$context = new HttpContext($app);

$server = new Server($_SERVER);

$request = new HttpRequest($server, $_GET, $_POST, $_FILES, $_COOKIE);

$response = new \Local\Core\Inner\BxModified\HttpResponse($context);

try
{
    if( !check_bitrix_sessid() )
    {
        throw new \Exception('Доступ запрещен');
    }

    // Роуты
    $rules = include __DIR__.'/routes/include.php';

    // Регистрируем роуты
    $routes = new RouteCollection();

    foreach( $rules as $path_name => $params )
    {

        $methods = $params['methods'] ? : ['GET'];

        /**
         * string $path,
         * array $defaults = array(),
         * array $requirements = array(),
         * array $options = array(),
         * ?string $host = '',
         * $schemes = array(),
         * $methods = array(),
         * ?string $condition = ''
         */
        $routes->add(
            $path_name,
            new Route(
                $params['path'], ['handler' => $params['handler']], (array)$params['args'], array(), '', array(), $methods
            )
        );
    }

    /**
     * Контекст запроса
     * Можно было создать примерно так - https://s.mail.ru/7FRR/5qDdbgh74
     * но создал по-нормальному
     */
    $context = new RequestContext();
    $context->fromRequest(Request::createFromGlobals());

    // Матчер урлов
    $matcher = new UrlMatcher($routes, $context);

    // Находим текущий роут
    $parameters = $matcher->match($context->getPathInfo());

    if( array_key_exists('handler', $parameters) )
    {
        $callable = $parameters['handler'];

        if( is_string($callable) )
        {
            if( strpos($callable, ':') !== false )
            {
                $callable = explode(':', $callable);
            }
        }

        if( is_callable($callable) )
        {

            $args = array_filter(
                $parameters,
                function($v, $k)
                    {
                        return !in_array($k, ['handler', '_route']);
                    },
                ARRAY_FILTER_USE_BOTH
            );

            call_user_func($callable, $request, $response, $args);
            $GLOBALS['APPLICATION']->RestartBuffer();
            $response->send();
            die();
        }
        else
        {
            // По факту говорил что метод и класс не найдены. Просто маскируем
            throw new \Exception('Внутренняя ошибка. Код: 300813');
        }
    }
}
catch( \Throwable $e )
{

    $GLOBALS['APPLICATION']->RestartBuffer();
    $response->setContentJson(
        ["error" => 'Возникло исключение '.get_class($e).' Тескт ошибки: '.$e->getMessage()],
        405
    );
    $response->send();
    die();
}

require( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php" );
