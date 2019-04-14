## \Local\Core\Inner\TradingPlatform\TradingPlatform

Это основной класс для работы с торговыми пощадками. Стоит воспринимать его как Sale\Order для работы с заказами. Он - единственная точка входа.

Примеры вызыва формы редактирования/создания ТП
```php
// Для новых ТП
$obTp = ( new \Local\Core\Inner\TradingPlatform\TradingPlatform );
try
{
    $obHandler = $obTp->getHandler('yandex_market');
    $obHandler->printFormFields();
}
catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e)
{
     echo 'Не удалось загрузить обработчик';
}
catch (\Throwable $e)
{
    echo $e->getMessage();
}


// Для созданных ранее ТП
$obTp = ( new \Local\Core\Inner\TradingPlatform\TradingPlatform );
try
{
    $obTp->load(1);
    $obHandler = $obTp->getHandler();
    $obHandler->printFormFields();
}
catch (\Local\Core\Inner\TradingPlatform\Exceptions\TradingPlatformNotFoundException $e)
{
    echo 'Не удалось загрузить ТП';
}
catch (\Local\Core\Inner\TradingPlatform\Exceptions\HandlerNotFoundException $e)
{
    echo 'Не удалось загрузить обработчик';
}
catch (\Throwable $e)
{
    echo $e->getMessage();
}
```

