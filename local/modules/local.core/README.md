## Мануал по проекту

#### Правила проекта
+ Никаких прямых обращений по ID, всегда обращаемся по CODE. Не важно - элемент это, ИБ или какой либо обработчик. Если нет символьного кода - делаем функционал, что бы был. Если обращаемся к HL - сверяемся с XML_ID. Это делается, что бы в случае пеереезда или удалении части данных можно было встановить все путем добалвения значений по символьному коду, не залезая в код и не переписывая ID. Вообще лучше глянуть **\Local\Core\Assistant**, там почти всегда можной найти необходимого ассистента. Ну а если нет - дописать.
+ Классы всегда начинаются с большой буквы, camelCase
+ Namespace всегда начинаются с большой буквы, camelCase
+ Методы всегда начинаются с маленькой буквы, camelCase
+ Ключи массива всегда UPPER CASE
+ Если указывается название какого-либо класса в качестве параметра в формате строки, то допустимо использовать только {classname}::class, без кавычек. Это необходимо для корректного поиска по проекту
+ Переменные и св-ва должны в первых буквах отображать свое содержимое.  Пример:
  + $arResult - > array
  + $obResult - > Object
  + $intCount - > integer
  + $strName - > string
  + $boolResult - > boolean
  + $funCalc() - > function
  + $classCalc() - > class
  + $mixData - > mixed
+ При обращении к **любым** классам писать его namespace. Пример: **\CFile::GetPath($intFileId)**
+ Не использовать конструкцию **use \\...\Class as OtherClass** если это не имеет жесткого обоснования
+ Если путь до класса по какой то причине хранится в база денных (к примеру агенты), поменять его пустым интерфейсом **\Local\Core\Inner\Interfaces\UseInDb**
+ Использовать PHPDoc. Это требование!
+ Агенты а так же воркеры должны иметь только 1 точку входа. В ней они должны вызывать другой метод, который отвечает за инициализацию логики кода, а не описывать ее в себе.
+ Мы работаем по psr0-prs4 с незначительным отклонением
+ Для построения страниц в админпанели использовать готовый внутренний класс **\Local\Core\AdminHelper**
+ Все классы и namespace пишутся в единственном лице, т.е. **\Local\Core\Inner\Client** а не **\Local\Core\Inner\Clients**
+ Все обработчики должны лежать в **\Local\Core\EventHandlers**, про **init.php** следует забыть от слова совсем! **При этом** все хэндлеры **ORM** должны быть описаны **в самом ORM класса**!

---
## Важная информация по ходу разработки
+ После каждого мержа смотреть в **\Local\Core\Inner\BxModified** на наличие новых классов. Создавая новый класс необходимо пройтись по проекту и применить его везде, согласовав с предидущим разработчиком. **Использовать переопределенные класса отсюда!**
+ Не, я серьзно. Мы DataManager даже расширили. Поэтому создавая **ORM** наследуйся от **\Local\Core\Inner\BxModified\Main\ORM\Data\DataManager**
+ Если ORM модуля **local.core** использует работу с **\CFile** или таблице **b_file** - дописать логику проверки в метод **getOrmFiles()**. Подробнее ниже в **\Local\Core\Inner\Fileman\Cleaner::clearUnregisteredLocalCoreFiles()**.
+ Каждый **ORM** должен содержать поля **DATE_CREATE** и **DATE_MODIFIED**.
+ Каждый **ORM**, который используется в компоненте, который кэширует результат, находящийся в области кэширования, должен иметь метод **public static function clearComponentsCache()** . Более подробно читать в **Local\Core\Model\Data**
+ Компоненты и другие куски кода, использующие кэширование, должны размещать кэш в своем пути. Подробнее читай в **\Local\Core\Inner\Cache**
---
## API | EASY

### \Local\Core\Model и тонкости ORM

Была проблема, что значения Fields\EnumField везде приходилось прописывать ручками.

Решением проблемы стало унаследование от **\Local\Core\Inner\BxModified\Main\ORM\Data\DataManager**. 

Там определены 2 метода:
+ **public static function getEnumFieldHtmlValues()** - получает коды значений и текст значений Fields\EnumField в формате **VALUE => TEXT** по коду поля.
+ **public static function getEnumFieldValues()** - получает только коды значений Fields\EnumField по коду поля.

Для работы необходимо в описании ORM определить **public static $arEnumFieldsValues**.

Пример описания:
```php
/** @see \Local\Core\Inner\BxModified\Main\ORM\Data\DataManager::$arEnumFieldsValues */
public static $arEnumFieldsValues = [
    'RESOURCE_TYPE' => [
        'LINK' => 'Ссылка на файл',
        'FILE' => 'Загрузить файл',
    ],
    'HTTP_AUTH' => [
        'Y' => 'Да',
        'N' => 'Нет'
    ]
];
```
Значение для **ACTIVE** задано по умолчанию, т.к. для **Model\Data** это обязательно поле!

Так же есть метод **public static function clearComponentsCache()** . Служит для скидывания кэша компонента. По умолчанию он пустой и вызывается через хэндлеры OnAfterUpdate и OnDelete (об этом ниже). При необхомости вызывается либо из-вне.

Пример тела метода:
```php
\Local\Core\Inner\Cache::deleteComponentCache(['personal.company.list'], [ 'user_id='.$arFields['USER_OWN_ID'] ]);
```

#### \CLocalCore::getOrmFieldsTable()
У главного класса local.core есть метод **getOrmFieldsTable()**, который позваляет сделать html структуру, описывая **getMap()**.

После любого внесения изменения в **getMap()** и его логические цепочки, необходимо вызвать
```php
echo \CLocalCore::getOrmFieldsTable( Local\Core\Model\Data\SiteTable::class );
```
А получившийся текст в ставить описание класса ORM, как это сделано сейчас. Это позволит смотреть поля, их типы и варианты не залезая в код класса. Чисто для PHPDoc'a.

#### \CLocalCore::createDBTableByGetMap()

Метод создает таблицу ORM по описанию **getMap()**. **Создавать таблицы надо через него!**

Так же еще есть **dropDBTableByGetMap()** и **resetDBTableByGetMap()**

---

### \Local\Core\Model\Data

**ORM сущности, которые предназначены для хранение данных по другим сущностям. Данные не должны быть справочными!** 

К примеру в **\Local\Core\Model\Data** нужно хранить список компаний, сайтов, фидов.

Каждый ORM **Model\Data** должен содержать поля **ID**, **ACTIVE**, **DATE_CREATE** и **DATE_MODIFIED**.

Так же ORM **Model\Data** должен содержать 2 обработчика и метод для инициализации сброса кэша компонента.

Ввиду этого, что бы точно ничего не забыть, необходимо, создавая класс в ORM **Model\Data**, скопировать **Model\Data\BaseOrmTable** и продолжить писать уже от того, что есть.

---

### \Local\Core\Model\Reference
**ORM сущности, которые являются справочниками чего либо.**

К примеру справочник единиц измерения.

Каждый ORM **Model\Reference** должен содержать поля **ID**, **DATE_CREATE**,**DATE_MODIFIED**, **SORT**, **NAME**, **CODE**.

Создавая справочник в ORM **Model\Reference** следует так же скопировать **Model\Reference\BaseOrmTable** и продолжить писать уже от того, что есть.

---

### \Local\Core\Inner\Cache
Данный класс не заменяет родной класс для кеширования! Но его функционал выходит за рамки ассистента, поэтому он вынесен в Inner.

Работая с кэшем мы сталкиваемся с 2мя проблемами:
+ Кэш лежит в птути хэшей и трудно найти нужный.
+ Его проблематично точечно уничтожить.

Для решения данной проблемы был создан **\Local\Core\Inner\Cache** и его 2 метода:
+ getCachePath( array $arDirPath, array $arParams = [] )
+ deleteCache( array $arDirPath, array $arParams = [] )

> Еще есть 2 метода
> + getComponentCachePath( array $arDirPath, array $arParams = [] )
> + deleteComponentCache( array $arDirPath, array $arParams = [] )
> 
> Они являются обертками над методами выше, автоматически дописывая в $arDirPath **components** в самое начало

Стоит понимать, что порядок значений в **$arDirPath** и **$arParams** играет важную роль, дальше будет понятно почему.

Каждый раз, создавая кэш, необходимо заправшивать у данного класса генерацию пути.

#### getCachePath()

Пример из компонента **personal.company.list**
```php
$obCache->startDataCache(
    ( 60 * 60 * 24 * 7 ),
    md5( __METHOD__.'_user_id='.$GLOBALS[ 'USER' ]->GetID().'_page='.$nav->getCurrentPage().'&offset='.$nav->getOffset() ),
    \Local\Core\Inner\Cache::getComponentCachePath( ['personal.company.list'], ['user_id='.$GLOBALS[ 'USER' ]->GetID(), 'page='.$nav->getCurrentPage().'&offset='.$nav->getOffset()] )
);
```
Используя данную конструкцию наш кэш будет записывать по пути
```
/bitrix
  /cache
    /local.core
      /components (потому что getComponentCachePath() сам дополняет "components" )
        /personal.company.list
          /user_id=1 (начались параметры)
            /page=1&offset=0
              / дальше хэш папки и хэш кэша
```
Такая архитектура удобна и наглядна.
> При работе с компонентами с построничкой необходимо всегда заполнять **page** и **offset**, что бы не плодить **page=&offset=** и **page=1&offset=0**, т.к. это одно и то же

#### deleteCache()
Продолжая пример и логику выше предположим, что данные в ORM CompanyTable поменялись, и теперь необходимо скинуть кэш компонента **personal.company.list**, но для пользователя с ID 1. Именно на этот случай мы в **$arParams** в **getComponentCachePath()** мы передавали параметры в порядке:
```php
['user_id='.$GLOBALS[ 'USER' ]->GetID(), 'page='.$nav->getCurrentPage().'&offset='.$nav->getOffset()]
```
Ориентируясь по структуре директорий, приведенной выше, мы понимаем, что необходимо очистить кэш от директории **/bitrix/cache/local.core/components/personal.company.list/user_id=1/**.

Поэтому мы вызываем метод **deleteComponentCache()** (если вне компонента, то **deleteCache()**) со следующими параметрами:
```php
\Local\Core\Inner\Cache::deleteComponentCache( ['personal.company.list'], ['user_id=1'] );
```
В итоге у нас очистятся все кэши по цепочке
```
/bitrix
  /cache
    /local.core
      /components (потому что deleteComponentCache() сам дополняет "components" )
        /personal.company.list
          /user_id=1
```

#### Заключение
По приведенным примерам выше становится понятно, почему порядок параметров в **$arDirPath** и **$arParams** так важен. **Особенно важно** думать на перед, пихая в **$arParams** параметры, а точнее не думать жопой и понимать, в каких случаях, в каком порядке и отталкиваясь от чего нам понадобится скидывать кэш.

И именно для автоматизации процесса скидывания **нужного** кэша в **нужное** мы в **ORM** классах объявляем **public static function clearComponentsCache()** и как минимум 2 хэндлера, которых его инициализируют.

---

### \Local\Core\Inner\AdminHelper
Для создания своих сраниц от модуля в админке необходимо сделать новый класс в **Local\Core\Inner\AdminHelper\Data**, если это используется **ORM**, либо другой, если появится такая нужда.
 
 Страница списка наследуетс от **\Local\Core\Inner\AdminHelper\EditBase**, деталки - от **\Local\Core\Inner\AdminHelper\ListBase** . Пример реализации можно глянуть в **Local\Core\Inner\AdminHelper\Data\Company** .
 
 **Все классы админки должны лежать в одном месте, в \Local\Core\Inner\AdminHelper !**

После создания класса его необходимо зарегистрировать в **local/modules/local.core/admin/admin_helper_route.php** и **local/modules/local.core/admin/menu.php** . Пример там же, все понятно.

---
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
### \Local\Core\Inner\BxModified
Данная директория предназначена для размещения классов, которые являются битрисковыми, но требовали доработки с нашей стороны. К примеру вместо **\CFile** необходимо теперь использовать **\Local\Core\Inner\BxModified\CFile**. 

Если класс тащит за собой цепочку других рядом стоящих классов, то их необходимо вынести и сгрупировать в подкатегорию по названию класса. К примеру **\Local\Core\Inner\BxModified\CFile\CFile**

Список таких классов постоянно пополняется, необходимо за ним следить.

---
### Компоненты

Все компонента local.core должны быть реализованы через класс и быть унаследованы от **\Local\Core\Inner\BxModified\CBitrixComponent**.

**\Local\Core\Inner\BxModified\CBitrixComponent** содержите 3 ключевых метода:
+ _checkCompanyAccess() - проверяет права пользователя на компанию
+ _checkSiteAccess() - проверяет права пользователя на сайт
+ _show404Page() - моментально прекращает работу компонента и выводит 404

**_checkCompanyAccess()** необходимо применять на всех компонентах, которые связаны с выводом данных компаний.

**_checkSiteAccess()** те же требования, что и к **_checkCompanyAccess()**, только еще надо проверять все компоненты, выводящие данные по сайтам компании. На компонентах сайтов применяются оба последовательно.

---
### \Local\Core\Inner\Route
Класс рассчитан на работу с путями по единому шаблону. 
Для корректной работы в корне сайта требуется создать файл **.routerewrite.php** и объявить внутри массив **$arLocalRoutes**. 
##### Структура массива и пример его реализации: 
```php
$arLocalRoutes = [
    'company' => [
        'list'   => [
            'URL'         => '/personal/company/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    $GLOBALS['APPLICATION']->AddChainItem(
                        "Мои компании",
                        \Local\Core\Inner\Route::getRouteTo(
                            'company',
                            'list'
                        )
                    );
                }
        ],
        'edit'   => [
            'URL'         => '/personal/company/#COMPANY_ID#/edit/',
            'BREADCRUMBS' => function($arParams = [])
                {
                    \Local\Core\Inner\Route::fillRouteBreadcrumbs(
                        'company',
                        'list'
                    );
                    $GLOBALS['APPLICATION']->AddChainItem('Редактирование компании '.\Local\Core\Inner\Company\Base::getCompanyName($arParams['COMPANY_ID']));
                }
        ],
    ]
];
```
В данном примере **company** - это ключ **$strDirectory**, **list** - **$strAction**.
Экшен - массив со следующими ключами:
+ URL - нужен для **getRouteTo()**, записывается урл, возможно размещение плэйсхолдера.
+ BREADCRUMBS - каллбек функция, нужна для **fillRouteBreadcrumbs()**, имеет параметр **$arParams**, в который передаютс параметры. В этой функции происходит построение цепочтки навигации. Предпочтительно использовать анонимные функции.

##### \Local\Core\Inner\Route::getRouteTo
Возвращает путь по роуту, заменяя плейсхолдеры.

Пример вызова с заменой плейсхолдеров из структуры выше:
```php
\Local\Core\Inner\Route::getRouteTo('company','list');
// /personal/company/


\Local\Core\Inner\Route::getRouteTo('company','edit', ['#COMPANY_ID#' => 12]);
// /personal/company/12/edit/
```

##### \Local\Core\Inner\Route::fillRouteBreadcrumbs
Заполняет breadcrumbs по роуту.

Пример вызова:
```php
\Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'list');

\Local\Core\Inner\Route::fillRouteBreadcrumbs('company', 'edit', ['COMPANY_ID' => $arParams['COMPANY_ID']]);
```
Предполагается вызывать его из **component_epilog.php**

---

### Ajax
Все ajax отправляются на **/ajax/**. Для работы с ajax следует использовать библиотеку **axios** ввиду ее легковесности, удобства настройки и Promise.

В **/ajax/routes/index.php** следует настроить правило обработки роута. По текущим реализациям там все понятно. Отправляя ajax надо передавать sessid. По умолчанию в axios его передача настроена, поэтому вписывать его каждый раз не имеет нужды.

Настроив роут в /ajax/routes/index.php нужный путь следует разместить обработчик в **/Local/Core/Ajax/Handler/** . Вызываемые методы должны быть статичны и публичны.

При написания хэндлера можно отталкиваться от Example.php, который там размещен там же, но на всякий продублируем сюда кусок код по удалению компании.
```php
public static function delete(\Bitrix\Main\HttpRequest $request, \Local\Core\Inner\BxModified\HttpResponse $response, $args)
{
    $arResult = [];

    if( !empty($args['company_id']) )
    {
        $intCompanyId = $args['company_id'];

        $ar = CompanyTable::getByPrimary($intCompanyId, ['filter' => ['USER_OWN_ID' => $GLOBALS['USER']->GetId()], 'select' => ['ID']])->fetch();
        if( !empty($ar['ID']) )
        {
            CompanyTable::delete($ar['ID']);
            $arResult['result'] = 'SUCCESS';
        }
    }

    if( empty($arResult['result']) )
    {
        $arResult['result'] = 'error';
    }

    $response->setContentJson($arResult);
}
```
Как видно из примера единственный способ отдать информацию из обработчика - это передача в **$response**. Из этого можно сделать вывод, что **все ответы ajax должен отдавать в формате JSON**, которые следует обработать на входе.

Пример функции, которая отправляет запрос ajax и обрабатывает его
```js
<script type="text/javascript">
    function wblDeleteCompany(intId) {
        axios.post('/ajax/company/delete/'+intId+'/')
            .then(function (response) {
                if( response.data.result == 'SUCCESS' )
                {
                    alert('OK!');
                }
                else
                {
                    alert('ERROR!')
                }
            })
    }
</script>
```

---
## API | MEDIUM

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
$rs = \Local\Core\Inner\JobQueue\Job::addIfNotExist($worker, $dateTime, 2);
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

### \Local\Core\Inner\Client\Dadata
DaData используется для получения информации по:
+ адресам
+ юр.лицам и индивидуальным предпринимателям

Пример вызова:
##### Для адресов:
```php
$query = new \Local\Core\Inner\Client\Dadata\Query;
$query
  ->set('query', 'г Химки, ул Германа Титова, дом 1 кв 1')
  ->set('count', 10);
$addressClient = new
\Local\Core\Inner\Client\Dadata\AddressClient();
$res = $addressClient->suggest($query);
```
##### Для юр.лиц
```php
$query = new \Local\Core\Inner\Client\Dadata\Query;
$query->set('query', 'Бринэкс');
$query->set('count', 10);
$legalClient = new \Local\Core\Inner\Client\Dadata\LegalClient();
$res = $legalClient->suggest($query);

$query = new \Local\Core\Inner\Client\Dadata\Query;
$query->set('query', '1650134050');
$query->set('type', 'LEGAL');
$query->set('branch_type', 'MAIN');
$query->set('count', 10);
$legalPartyClient = new \Local\Core\Inner\Client\Dadata\LegalPartyClient();
$res = $legalPartyClient->suggest($query);
```

---
### \Local\Core\Inner\Fileman\Cleaner::clearUnregisteredLocalCoreFiles()

Данный метод отвечает за чистку структуры **b_file** модуля **local.core** . Вызывается на агенте автоматически.

Если **ORM** класс содержит в себе работу с файлами (как **\Local\Core\Model\Data\SiteTable**), то для него необходимо определить публичный статичный метод **getOrmFiles()**. 

Пример реализации на примере **\Local\Core\Model\Data\SiteTable**
```php
/**
 * Метод возвращает объект подготовленный \Bitrix\Main\ORM\Query\Result
 *
 * @return \Bitrix\Main\ORM\Query\Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
public static function getOrmFiles()
{
    return self::getList([
        'filter' => [
            '!FILE_ID' => false,
        ],
        'select' => ['FILE_ID']
    ]);
}
```
Говоря проще - он должен вернуть **\Bitrix\Main\ORM\Query\Result** от своего **getList()**. В **select** должны быть указаны только поля, **содержащие ID файла из b_file**!

Для лучшего понимания, как это работает, следует изучить **\Local\Core\Inner\Fileman\Cleaner::__checkAndClearOrmFiles()**.
