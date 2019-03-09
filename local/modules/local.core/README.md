## Мануал по проекту

#### Правила проекта
+ Никаких прямых обращений по ID, всегда обращаемся по CODE. Не важно - элемент это, ИБ или какой либо обработчик. Если нет символьного кода - делаем функционал, что бы был. Если обращаемся к HL - сверяемся с XML_ID. Это делается, что бы в случае пеереезда или удалении части данных можно было встановить все путем добалвения значений по символьному коду, не залезая в код и не переписывая ID. Вообще лучше глянуть **\Local\Core\Assistant**, там почти всегда можной найти необходимого ассистента. Ну а если нет - дописать.
+ Классы всегда начинаются с большой буквы, camelCase
+ Namespace всегда начинаются с большой буквы, camelCase
+ Методы всегда начинаются с маленькой буквы, camelCase
+ Ключи массива всегда UPPER CASE
+ **private** св-ва и методы класса должны помечаться ввиде 2х нижних подчеркиваний перед названием ( **private $__arExport** )
+ **protected** св-ва и методы класса должны помечаться ввиде 1 нижнего подчеркивания перед названием ( **protected $_arExport** )
+ Если указывается название какого-либо класса в качестве параметра в формате строки, то допустимо использовать только {classname}::class, без кавычек. Это необходимо для корректного поиска по проекту
+ Переменные и св-ва должны в первых буквах отображать свое содержимое.  Пример:
  + $arResult - > array
  + $obResult - > Object
  + $intCount - > integer
  + $strName - > string
  + $boolResult - > boolean
  + $funCalc() - > function
  + $classCalc() - > class
+ При обращении к **любым** классам писать его namespace. Пример: **\CFile::GetPath($intFileId)**
+ Не использовать конструкцию **use \\...\Class as OtherClass** если это не имеет жесткого обоснования
+ Если путь до класса по какой то причине хранится в база денных (к примеру агенты), поменять его пустым интерфейсом **\Local\Core\Inner\Interfaces\UseInDb**
+ Использовать PHPDoc. Это требование!
+ Агенты а так же воркеры должны иметь только 1 точку входа. В ней они должны вызывать другой метод, который отвечает за инициализацию логики кода, а не описывать ее в себе.
+ Мы работаем по psr0-prs4 с незначительным отклонением
+ Для построения страниц в админпанели использовать готовый внутренний класс **\Local\Core\AdminHelper**

---
## API и информация

### \Local\Core\Assistant
Это область ассистента. В ней предполагается хранение классов-ассистентов, которые помогают быстро получить какие либо данные. К примеру **\Local\Core\Assistant\Iblock\Iblock::getIdByCode()** помогает получить ID ИБ по его коду и коду Типа ИБ, в котором он находится. При этом результат ассистента должен быть короткий, оптимизированный и либо кешироваться, либо записываться в регистр класса (на примере **\Local\Core\Assistant\HighLoadBlock\HighLoadBlock::getEntity()**)

---
### \Local\Core\Agent\Base
Все агенты должны быть унаследованы от **\Local\Core\Agent\Base**.
Вызов агента происходит путем вызова **\Local\Core\Agent\Base::init()**

---
### \Local\Core\EventHandlers\Base
Все обработчики должны быть инициализированы в **\Local\Core\EventHandlers\Base** и располагаться в методе, который назвывается как модуль, к котому относится обработчик.
Пример:
```php
private static function registerMain()
{
    $eventManager = EventManager::getInstance();

    /** @see \Local\Core\EventHandlers\Main\OnBeforeProlog::initializeRegionHost() */
    $eventManager->addEventHandler('main', 'OnBeforeProlog', [Main\OnBeforeProlog::class, 'initializeRegionHost']);

}
```

---
### local/tools/console

Основной скрипт-контроллер всех консольных команд. Главным преимущесвтом использования модуля symfony/console является то, что многие рутинные задачи в ней уже решены и имеется возможность увидеть весь список доступных консольных команд. Для этого необходимо в папке скрипта выполнить команду (**local/tools/**):
```
php console list
```
Вызов конкретной команды осуществляется следующим образом:
```
php -d mbstring.func_overload=2 console <command> [options] [arguments]

ex: php console kd:demo 'MyName'
```
 
Написать свою компанду можно, разместив ее в **\Local\Core\Console\Command** по примерну **\Local\Core\Console\Command\DemoConsole**.

---
### \Local\Core\Inner\JobQueue
##### Очередь задач
Раннер - некий демон, запускающий воркеры.
Воркеры(у нас) - некий классы, реализующие конкретную задачу. Исполняются в режиме cli, действуют ограничения агентов битрикса, кроме возврата собственного имени для переинициализации.
Все задачи хранятся в таблице **a_data_job_queue**

### Воркер
Каждый воркер расширяет абстрактный класс **\Local\Core\Inner\JobQueue\Abstracts\Worker**

Пример воркера **\Local\Core\Inner\JobQueue\Worker\Example**, всё описано phpDoc’ом, рекомендую посмотреть. 

##### Добавление воркера
Через метод **\Local\Core\Inner\JobQueue\Add::job()**
```php
$worker = new \Local\Core\Inner\JobQueue\Worker\Example(['твоиДанные'=>'воВремя','исполнения','воркерв']);
$dateTime = new \Bitrix\Main\Type\DateTime();
$dateTime->add('+ 3600 sec');
$rs = \Local\Core\Inner\JobQueue\Job::add($worker, $dateTime, 2);
if ($rs->isSuccess()) {
    //...
}
```

##### Свой воркер
Обязательно определить метод **doJob()**, который возвращает **\Bitrix\Main\Result**.
Внутри можно получить входные данные через **$this->getInputData()**;
Если результат успешен, текущая работа будет помечена S - success.

Если есть ошибки, результат будет помечен E - error. Воркер будет перезапущен через 2 минуты. 

Время следующего запуска можно переопределить через метод getNextExecuteAt().

Если требуется финализировать провал и запусков больше не требуется, можно выбросить **\Local\Core\Inner\JobQueue\Exception\FailException('Финалочка');**

Работа будет отмечена статусом F - fail.
> **Важно.**
> 
> Все выброшенные исключения внутри doJob() попадут в лог.
> Все ошибки в \Bitrix\Main\Result требуется логировать разработчику воркера.

Пример:
```php
class Example extends \Local\Core\Inner\JobQueue\Abstracts\Worker implements Inner\Interfaces\UseInDb
{
    /**
     * {@inheritdoc}
     */
    public function doJob(): \Bitrix\Main\Result
    {
        $result = new \Bitrix\Main\Result();
        $arInputData = $this->getInputData();

        //Some php code

        //You can
        //throw New \Exception('Some');
        //Or
        //$result->addError(new \Bitrix\Main\Error('Some Error'));

        //If need final, without success
        //throw new \Local\Core\Inner\JobQueue\Exception\FailException('Финалочка');

        //All other throw \Throwable will be logged as critical
        //All \Bitrix\Main\Error in Result will NOT be logged. It's your issue

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getNextExecuteAt(int $addSecond = 120): Main\Type\DateTime
    {
        //Some Another logic
        return parent::getNextExecuteAt($addSecond);
    }

}
```

##### Запуск воркера для debug’а.
После добавления воркера, смотрим в таблице его **ID**. А также **EXECUTE_BY** (скорее всего **NONE**).

В консоли своего дева пишем ```php -d mbstring.func_overload=2  local/tools/console worker 1002 NONE``` (последние 2 параметра **ID** и **EXECUTE_BY**).

Опционально можно возвращать ошибку и значительно увеличить количество попыток.

### Ранер
Запускает воркеры.

Запуск ранера:

```php -d mbstring.func_overload=2  local/tools/console runner```

**Запускать, в общем-то, нельзя!!!**
Возможно даже отключить на девах возможность инициализации.
Сейчас работает на компоненте симфони https://symfony.com/doc/current/components/process.html

---


