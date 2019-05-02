## Структура и классы

#### \Local\Core\Inner\TradingPlatform\TradingPlatform

Это основной класс для работы с торговыми пощадками. Стоит воспринимать его как Sale\Order для работы с заказами. Он - единственная точка входа для работы с ТП.

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
---
#### \Local\Core\Inner\TradingPlatform\Export

Этот класс для создания экпорта настроеной ТП.

Содержит 2 метома - **execute($intId)** для создания файл и **createQueue()** для создания очереди среди активных ТП, которые давно не обновлялись.

---
#### \Local\Core\Inner\TradingPlatform\MonthlyPayment

Это класс инициализации списания средст со счета за активные ТП, с автопродлением на месяц. Вызывается в агенте.

---
#### \Local\Core\Inner\TradingPlatform\Handler\
Область для хранения хэндлеров.

---
#### Local\Core\Inner\TradingPlatform\Field\
Область для полей хэндлеров, которые выводятся в форму редактирования обработчика, и от которых идет логика извлечения значений. Все поля должны наследоваться от **\Local\Core\Inner\TradingPlatform\Field\AbstractField**.


---
## Написание полей
Для хранения информации, правил обработки и извлечении значения в хэндлерах используются поля (филды).

 Все филды должны наследоваться от **\Local\Core\Inner\TradingPlatform\Field\AbstractField**, располагаться в **\Local\Core\Inner\TradingPlatform\Field**. Это позволяет сохранить единый интерфейс.
 
 Общие методы:
 + **Сэттеры и геттеры. Все геттеры  и сеттеры обязаны быть public, а их регистры - protected.**
 + setTitle() \ getTitle() - задает заголовок поля
 + setDescription() \ getDescription() - задает описания поля (tooltip)
 + setName() \ getName() - задает аттрибут **name** у поля
 + setRowHash() \ getRowHash() - задает хэш строки (для обновления поля по аяксу используется этот самый хэш. Генеририуется при setName(), но может быть переинициализирован в случае вложенности и многостурктурности филдов, таких как Resource)
 + setValue() \ getValue() - задает значение поля
 + setIsRequired(bool) \ getIsRequired() - задает признак обязательности поля. Не должен прописывать физическое **required** в поле, служит для валидации заполнености значений перед созданием файла экспорта.
 + setIsMultiple(bool) \ getIsMultiple() - задает признак множественности поля. Не всегда его нужно реализовывать, поэтому не всегда дает 100% гарантии работы
 + setIsReadOnly(bool) \ getIsReadOnly() - задает признак, что поле должно выводится только для чтения. При этом под полем выводить все его значения в input type="hidden", что бы не потерять их.
 + setEpilog() \ getEpilog() - задает эпилог. Эпилогом выступает новое поле, которое выводится после основного поля в форме.
 + setEvent() \ getEventCollected() - задает события (эвенты) на поле, такие как onclick и т.п. У setEvent() описан хороший phpDoc, читать его. Извлекать значения рекомендуется через **getEventCollected()**, т.к. он собирает их в строку и экранирует.
 + **Рэндеринг. Рендер - это св-во protected $_renderHtml, выступает как хранилище html филда, который формируется при вызове отрисовки поля ( getRender(), getRow(), printRow() )**
 + addToRender() - Добавляет html филда в ренден (хранилище html у поля)
 + resetRender() - Очищает рэндер филда
 + getRender() - Собирает рендер поля по заявленным в него параметрам и отдает его. Не отдает заголовок и описание, но отдает эпилог. Возвращает string html.
 + getRow() - getRender() + оборачивает его в строку в хэшем, заголовком и описанием. Он писался для извлечения поля в форму. Возвращает string html. 
 + printRow() - getRow() + выводит полученно на экран
 + **Другие методы**
 + isValueFilled() - производит валидаци поля. Читай phpDoc в \Local\Core\Inner\TradingPlatform\Field\AbstractField::isValueFilled()
 + extractValue() - извлекает конечное значение из поля по его параметрам. Читай phpDoc в \Local\Core\Inner\TradingPlatform\Field\AbstractField::extractValue()
 
 Допускается дописывать в филды геттеры и сеттеры, необходимые ему. К примеру у филда **Select** это **setOptions() / getOptions()**. Если потребность в одинаковым геттере\сеттере появляется более чем у одного поля, их необходимо выносить в трейты **Local\Core\Inner\TradingPlatform\Field\Traits**. Дополнять ими абстрктрый филд не нужно, что бы не создавать мусорную структуру. Идея простая - абстрактрый филд должен имееть только те геттеры/сеттеры и методы, которые будут импользовать у **всех** филдов! Исключение филды **Header**, **Condition**, **Infoblock**.
 
 Если филду требуется подполе, которое самостоятельно не будет использоваться, то его необходимо размещать в **\Local\Core\Inner\TradingPlatform\Field\Subfield**, что бы не мешать с основными. К такому примеру относится филд **Resource** и его сабфилд **ResourceBuilder** для реализации логики билдера, об этом позже.
 
 Если необходимо создать дополнительный филд, то необходимо прописать для него логику всех возможных для него вариантов развития событий, отталкиваясь от входных параметров. К примеру для филда **InputText** предусмотрен вариант **multiple** - в случаем множественности мы меняем его рендеринг с одно input на btn-group с возможностью добавлять и удалять строки, а **name** дополняем **[]** и т.п.
 
 Пример создания поля:
 ```php
 (new Field\Select())->setTitle('Конвертация цен')
                 ->setName('HANDLER_RULES[@handler_settings][CONVERT_CURRENCY_TO]')
                 ->setIsRequired()
                 ->setOptions([
                     'NOT_CONVERT' => 'Оставлять цены в переданных валютах',
                     'RUB' => 'Конвертировать в "Российский рубль"',
                     'BYN' => 'Конвертировать в "Белорусский рубль"',
                     'UAH' => 'Конвертировать в "Гривна"',
                     'KZT' => 'Конвертировать в "Тенге"',
                     'EUR' => 'Конвертировать в "Евро"',
                     'USD' => 'Конвертировать в "Доллар США"',
                 ])
                 ->setValue($this->getHandlerRules()['@handler_settings']['CONVERT_CURRENCY_TO'] ?? 'NOT_CONVERT')
                 ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
 Конвертация валюты будет происходить на основании курсов, предоставленным сервисом https://www.currencyconverterapi.com/ .<br/>
 Если курсы данного сервиса Вам не устравивают, Вы можете самостоятельно сконвертировать Валюты, передать их в Robofeed XML и выбрать в данном поле <b>"Оставлять цены в переданных валютах"</b>.
 DOCHERE
                 ))
 ```
 
 #### Подробнее о филдах
 
 #### InputText
 Выводит обычный input type="text". Ничего интересного.
 
 Пример:
 ```php
 (new Field\InputText())->setTitle('Ссылка на сайт')
     ->setName('HANDLER_RULES[@handler_settings][DOMAIN_LINK]')
     ->setIsRequired()
     ->setValue($this->getHandlerRules()['@handler_settings']['DOMAIN_LINK'] ?? '')
     ->setPlaceholder('https://example.com')
 ```
 ---
 #### InputHidden
 Выводит input type="hidden". Не имеет логики использовать его самостоятельно, рассчет был на использовании его в эпилоге или внутри другого поля. Ввиду этого **getRow()** переинициализирован.
 
 Пример:
 ```php
 (new InputHidden())->setValue('12')
     ->setName('TP_ID')
 ```
 ---
 #### Select
 Селект он и в африке селект. Но стоит знать, что **extractValue()** у селекта возвращает именно value, а не текст option, что логично, но иногда можно затупить.
 
 Пример:
 ```php
  (new Field\Select())->setTitle('Конвертация цен')
      ->setName('HANDLER_RULES[@handler_settings][CONVERT_CURRENCY_TO]')
      ->setIsRequired()
      ->setOptions([
          'NOT_CONVERT' => 'Оставлять цены в переданных валютах',
          'RUB' => 'Конвертировать в "Российский рубль"',
          'BYN' => 'Конвертировать в "Белорусский рубль"',
          'UAH' => 'Конвертировать в "Гривна"',
          'KZT' => 'Конвертировать в "Тенге"',
          'EUR' => 'Конвертировать в "Евро"',
          'USD' => 'Конвертировать в "Доллар США"',
      ])
      ->setValue($this->getHandlerRules()['@handler_settings']['CONVERT_CURRENCY_TO'] ?? 'NOT_CONVERT')
      ->setEpilog((new Field\Infoblock())->setValue(<<<DOCHERE
Конвертация валюты будет происходить на основании курсов, предоставленным сервисом https://www.currencyconverterapi.com/ .<br/>
Если курсы данного сервиса Вам не устравивают, Вы можете самостоятельно сконвертировать Валюты, передать их в Robofeed XML и выбрать в данном поле <b>"Оставлять цены в переданных валютах"</b>.
DOCHERE
      ))
 ```
 
 #### Textarea
 Логика реализации от InoutText почти полностью, но это textarea.
 
 Пример:
 ```php
 (new Field\Textarea())->setTitle('Расскажите о себе')
     ->setName('HANDLER_RULES[@handler_settings][DOMAIN_LINK]')
     ->setIsRequired()
     ->setValue($this->getHandlerRules()['@handler_settings']['DOMAIN_LINK'] ?? '')
     ->setPlaceholder('Расскажите о себе')
 ```
 
 ---
 #### Header
 Поле заголовка, разделяющие поля на логичные блоки. **getRow()** изменен, но при этом филд используется самостоятельно. Из всех методов используется только **setValue()**.
 
 Пример:
 ```php
 (new Field\Header())->setValue('Настройки обработки')
 ```
 ---
 #### Infoblock
 Блок, который служил для уточнения данных, вывода ошибки или что то такое. По факту div.alert от bootstrap, которому можно задать один из нескольких типов. Подробнее в нем можно почитать. Используется обычно в эпилоге, но можно вывести и самостоятельно, хотя хз зачем.
 
 Пример:
 ```php
  (new Field\Select())->setTitle('Конвертация цен')
      ->setName('HANDLER_RULES[@handler_settings][CONVERT_CURRENCY_TO]')
      ->setIsRequired()
      ->setOptions([
          'NOT_CONVERT' => 'Оставлять цены в переданных валютах',
          'RUB' => 'Конвертировать в "Российский рубль"',
          'BYN' => 'Конвертировать в "Белорусский рубль"',
          'UAH' => 'Конвертировать в "Гривна"',
          'KZT' => 'Конвертировать в "Тенге"',
          'EUR' => 'Конвертировать в "Евро"',
          'USD' => 'Конвертировать в "Доллар США"',
      ])
      ->setValue($this->getHandlerRules()['@handler_settings']['CONVERT_CURRENCY_TO'] ?? 'NOT_CONVERT')
      ->setEpilog(
      (new Field\Infoblock())->setValue(<<<DOCHERE
Конвертация валюты будет происходить на основании курсов, предоставленным сервисом https://www.currencyconverterapi.com/ .<br/>
Если курсы данного сервиса Вам не устравивают, Вы можете самостоятельно сконвертировать Валюты, передать их в Robofeed XML и выбрать в данном поле <b>"Оставлять цены в переданных валютах"</b>.
DOCHERE
        )
      )
 ```
 ---
 #### Condition
 Блок условия. К примеру используется в Resource в "сложном условии", или в форме редактирования/создания ТП для выставления фильтрации товаров. Чесно сворован у битриксового модуля Sale и переписан под наши нужды. Общего у него с битриксовым теперь мало что есть, но тем не менее.
 
 Пример:
 ```php
 (new \Local\Core\Inner\TradingPlatform\Field\Condition())
     ->setTitle('Фильт товаров')
     ->setStoreId($arParams['STORE_ID'])
     ->setName('TP_DATA[PRODUCT_FILTER]')
     ->setValue($arResult['TP_DATA']['PRODUCT_FILTER'] ?? [])
 ```
 ---
 #### Resource
 Самый частый, жирный и заебный фибл. Является филдом, в котором юзвер выбирает источник данных.
 
 Всего источников 6, все они занесены в константы филда
 + SOURCE - Поле Robofeed XML
 + SIMPLE - Простое значение
 + BUILDER - Сложное значение
 + SELECT - Выбрать из списка
 + LOGIC - Сложное условие
 + IGNORE - Игнорировать поле
 
 Поподробнее о каждом по наростающей:
 
##### IGNORE
Игнорировать поле. Используется для того, что бы пропусить поле.
 
##### SIMPLE

Источник "Простое значение". Дает возможность заполнить статичное значение. Для того, что бы вывелся этот вариант необходимо передать в филд ресурса филд **InputText или Textarea**. Пример: 
```php
->setSimpleField((new Field\InputText()))
```
Передать в филд InputText/Textarea **setName()**, **setValue()** и **setIsRequired()** не имеет смысла - филд ресурса все равно все перетрет. Можно передать в него placeholder, multiple или что то в таком дух. 

##### SELECT

Источник "Выбрать из списка". Дает выбрать один из нескольких предоставленных вариантов. Для того, что бы этот вариант вывелся, необходимо педелать филд **Select** с возможными вариантами. Пример:
```php
->setSelectField((new Field\Select())->setOptions([
        'Y' => 'В наличии',
        'N' => 'Под заказ',
    ]))
```

##### SOURCE

Источник "Поле Robofeed XML". Дает возможность выбрать одно (или множество при multiple) значение из полей offer Robofeed XML, а точнее базовых полей, доставки, самовывоз и параметров. 

Для того, что бы этот источник заработал, необходимо в филд передать ID магазина:
```php
->setStoreId($this->getTradingPlatformStoreId())
```

##### BUILDER
Источник "Сложное значение". Калоборация SIMPLE и SOURCE, которая позваляет задать тексовое значение в маркерами от SOURCE. Используется к примеру для создания динамичного описания. В дальнейшем называется "билдер".

Для того, что бы билдер заработал, необходимо в филд передать ID магазина:
```php
->setStoreId($this->getTradingPlatformStoreId())
```

##### LOGIC
Источник "Сложное условие". При использовании создает логические варианты ты источников. Используется, к примеру, для выбора задать старую цену или пропустить поле. Возвожными вариантами выводит все остальные заданные источники.

Если филд помечен как обязательный - игнорирует вариант IGNORE.

Для того, что бы билдер заработал, необходимо передать в филд другие источники и ID магазина:
```php
->setStoreId($this->getTradingPlatformStoreId())
```
---
##### Пример филда Resource

Особенностию филда Resource, и вообще логикой его создания, выступает возможность преопределить значения для ТП. Разберем описанное поле в хэндлере ЯМаркета:
```php
$arFields['shop__offers__offer__name'] = (new Field\Resource())->setTitle('Полное название предложения')
    ->setDescription('Полное название предложения, в которое входит: тип товара, производитель, модель и название товара, важные характеристики.')
    ->setStoreId($this->getTradingPlatformStoreId())
    ->setName('HANDLER_RULES[shop][offers][offer][name]')
    ->setIsRequired()
    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['name'])
    ->setAllowTypeList([
        Field\Resource::TYPE_SOURCE,
        Field\Resource::TYPE_BUILDER,
        Field\Resource::TYPE_LOGIC,
    ])
    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['name'] ?? [
            'TYPE' => Field\Resource::TYPE_SOURCE,
            Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#FULL_NAME'
        ]);
```
Через метод **setAllowTypeList()** мы даем понять филду, какие источники следует выводить. Логично предположить, что названия товаров должны быть разными, поэтому здесь нет SIMPLE и SELECT, но его можно извлеч из Robofeed XML или сделать через билдер, поэтому мы передаем в филд SOURCE и BUILDER. Если вынуть голову из жопы и думать логически, то в построении списка разрешенных источников в филде ресурса проблем не возникнет.

---
##### Задание значений по умолчанию в филд Resource
Ввиду специфики этого филда рекомендуется всегда передавать в него значение по умолчанию. 

Если в филд InputText мы можем передать логичные string или array, то у филда Resource своя структура.

Пример стурктуры значения Resource с выбранным источником SIMPLE и значением "test":
```php
[
    'TYPE' => 'SIMPLE',
    'SIMPLE_VALUE' => 'test'
]
```
Как можно понять из примера - источник задается в ключе **TYPE**. Далее идет значение данного источника, с префиксом его типа. Но в силу постоянных модификаций ядра не рекомендуется передавать значения напрямую. Следует работать от констант. Правильный пример:
```php
[
    'TYPE' => Field\Resource::TYPE_SIMPLE,
    Field\Resource::TYPE_SIMPLE.'_VALUE' => 'test'
]
```
Это необходимо запомнить.

---
Пример значения источника SELECT с множестенным типом:
```php
[
    'TYPE' => Field\Resource::TYPE_SELECT,
    Field\Resource::TYPE_SELECT.'_VALUE' => ['Q1', 'W2']
]
```

---
Пример значения источника SOURCE:
```php
[
    'TYPE' => Field\Resource::TYPE_SOURCE,
    Field\Resource::TYPE_SOURCE.'_VALUE' => 'BASE_FIELD#FULL_NAME'
]
```
Полный список возможных значений SOURCE лечше всего посмотреть в коде html поля в select, в который они все и выводятся. Ничего умнее не предложу.

---
Пример значения источника BUILDER со значением "#Название товара# купить в москве от #Цена товара# рублей":
```php
[
    'TYPE' => Field\Resource::TYPE_BUILDER,
    Field\Resource::TYPE_TYPE_BUILDER.'_VALUE' => '{{BASE_FIELD#NAME}} купить в москве от {{BASE_FIELD#PRICE}} рублей'
]
```
Как можно заметить в значение билдера передается значение от SOURCE, обернутое в {{ }}. Имено по этой обертке код понимает, что это не просто текст, а маркер от SOURCE, и при извлечении значения заменяет его.

---
Пример LOGIC самый сложный. Возьмем для примера дефолтное значение габаритов в ЯМаркете:
```php
[
    'TYPE' => Field\Resource::TYPE_LOGIC,
    Field\Resource::TYPE_LOGIC.'_VALUE' => [
        'IF' => [
            [
                'RULE' => [
                    'CLASS_ID' => 'CondGroup',
                    'DATA' => [
                        'All' => 'AND',
                        'True' => 'True',
                    ],
                    'CHILDREN' => [
                        0 => [
                            'CLASS_ID' => 'CondGroup',
                            'DATA' => [
                                'All' => 'AND',
                                'True' => 'True',
                            ],
                            'CHILDREN' => [
                                1 => [
                                    'CLASS_ID' => 'CondProdWidth',
                                    'DATA' => [
                                        'logic' => 'Great',
                                        'value' => 0,
                                    ],
                                ],
                                2 => [
                                    'CLASS_ID' => 'CondProdHeight',
                                    'DATA' => [
                                        'logic' => 'Great',
                                        'value' => 0,
                                    ],
                                ],
                                3 => [
                                    'CLASS_ID' => 'CondProdLength',
                                    'DATA' => [
                                        'logic' => 'Great',
                                        'value' => 0,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'VALUE' => [
                    'TYPE' => Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_BUILDER.'_VALUE' => '{{BASE_FIELD#LENGTH}}/{{BASE_FIELD#WIDTH}}/{{BASE_FIELD#HEIGHT}}'
                ]
            ]
        ],
        'ELSE' => [
            'VALUE' => [
                'TYPE' => Field\Resource::TYPE_IGNORE
            ]
        ]
    ]

]
```
Запутано. Опишем значение попроще, с комментариями
```php
[
    'TYPE' => Field\Resource::TYPE_LOGIC,
    Field\Resource::TYPE_LOGIC.'_VALUE' => [
        'IF' => [
            0 => [
                'RULE' => [...], // Тут находится условие от Condition
                'VALUE' => [
                    'TYPE' => Field\Resource::TYPE_BUILDER,
                    Field\Resource::TYPE_BUILDER.'_VALUE' => '{{BASE_FIELD#LENGTH}}/{{BASE_FIELD#WIDTH}}/{{BASE_FIELD#HEIGHT}}'
                ]
            ]
        ],
        'ELSE' => [
            'VALUE' => [
                'TYPE' => Field\Resource::TYPE_IGNORE
            ]
        ]
    ]

]
```
Стуктура стала понятнее.

У значение логического источника есть 2 ветки - **IF** и **ELSE**.

**IF** принимает в значение массив. Это позволяет сделать логику формата "if ... elseif ... elseif ...". У каждого if/elseif в значении идет 2 ключа - **RULE**, которое содержет правило **ПРЕОБРАЗОВАННОГО** \Local\Core\Inner\Condition\Base, и ключ **VALUE**, которое описывает источник в случае, если этот вариант успешный. В нашем примере в случае успеха (проверка на ширина > 0 && высота > 0 && длина > 0) подключается билдер, который собирает строку {{BASE_FIELD#LENGTH}}/{{BASE_FIELD#WIDTH}}/{{BASE_FIELD#HEIGHT}} .

**ELSE** в отличии от IF сразу имеет значение с ключем **VALUE**, потому что перебирать ему нечего, это логический конец сложного условия. **VALUE** так же описывает источник, в нашем случае - игнорирует поле.

Ты спросишь "а как мне получить заранее готовое значение **RULE** в IF ?". Для этого сделай левую страницу, вызови туда Condition, выстави необходимые значения, отправь форму и полученный результат вставь в **RULE**. Вот код формы:

```php
<?
$intStoreId = 8;
$strInputName = 'INPUT_NAME';
?>
<form method="post" id="qwqwqw">
    <?
    $arParsedValue = \Local\Core\Inner\Condition\Base::parseCondition(\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getPost($strInputName), $intStoreId);
    if( empty($arParsedValue) )
    {
        $arParsedValue = [];
    }
    ?>
    <pre><?print_r($arParsedValue);?></pre>
    <?
        echo \Local\Core\Inner\Condition\Base::getConditionBlock($intStoreId, 'qwqwqw', 'werwer', $strInputName, $arParsedValue);
    ?>
    <button>send</button>
</form>

Должен быть результат вида:

Array
(
    [CLASS_ID] => CondGroup
    [DATA] => Array
        (
            [All] => AND
            [True] => True
        )

    [CHILDREN] => Array
        (
            [0] => Array
                (
                    [CLASS_ID] => CondProdArticle
                    [DATA] => Array
                        (
                            [logic] => Equal
                            [value] => 12
                        )

                )

        )

)
```

---
## Написание хэндлера

Хэндлеры необходимо размещать в **\Local\Core\Inner\TradingPlatform\Handler**. Названием хэндлера выступает его namespace. В директории хэндлера должен находиться описывающий его класс **Handler**. Таким образом обработчки ЯМаркета находится по пути **\Local\Core\Inner\TradingPlatform\Handler\YandexMarket\Handler**.

Все хэндрелы обязаны наследоваться от **\Local\Core\Inner\TradingPlatform\Handler\AbstractHandler** и описывать все его абстрактрые методы.

После создания класса обработчика его следует прописать в фабрику **\Local\Core\Inner\TradingPlatform\Factory** в методы **factory** (код должен быть идентичен getCode() хэндлера) и **getFactoryList** для вывода списка хэндлеров (так же служит для скрытия еще не готовых хэндлеро, поэтому если хэндлер не готов, нужды вписывать его нет, но перед продом добавить нужно будет).

Остановимся на основных методах хэндлера поподробнее.

---
##### getHandlerFields()
Метод, который должен отдавать массив филдов хэндлера (map). К примеру:
```php
$arFields = [
    '#header_y4' => (new Field\Header())->setValue('Торговые предложения')
];

$arFields['shop__offers__offer__store'] = (new \Local\Core\Inner\TradingPlatform\Field\Resource())->setTitle('Возможность купить товар без предварительного заказа')
    ->setDescription('При выбранном значении <b>"Игнорировать поле"</b> значение не будет передано. Если в личном кабинете ЯндексМаркета указана соответствующая точка продаж (торговый зал, пункт выдачи), то он автоматически воспримет покупку как возможную.')
    ->setStoreId($this->getTradingPlatformStoreId())
    ->setName('HANDLER_RULES[shop][offers][offer][store]')
    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['store'])
    ->setAllowTypeList([
        Field\Resource::TYPE_SELECT,
        Field\Resource::TYPE_LOGIC,
        Field\Resource::TYPE_IGNORE,
    ])
    ->setSelectField((new Field\Select())->setOptions([
        'Y' => 'Товар можно купить без предварительного заказа',
        'N' => 'Товар нельзя купить без предварительного заказа'
    ]))
    ->setValue($this->getHandlerRules()['shop']['offers']['offer']['store'] ?? [
            'TYPE' => Field\Resource::TYPE_SELECT,
            Field\Resource::TYPE_SELECT.'_VALUE' => 'Y'
        ]);
        
return $arFields;
```

Стоит заметить 2 важные вещи:
+ setName() всегда должен начинаться с HANDLER_RULES и быть многомерным массивом
+ ключи массива, в который передается филд, должен выгядить как структура setName(), но без HANDLER_RULES, соединяя ключи двумя _ . Это делается для упрощенного извлечения значений в дальнейшем коде.

Если необходимо создавать динамичные поля, как к примеру в ЯМаркете, то следует проверить выставленные значения. Для этого нужно использовать **$this->getHandlerRules()**, который передает загруженные через load() данные по ТП (при отрисовке в форме это данные, загруженные от ТП, при аяксе эти данные задаются от всей формы). Метод getHandlerRules() возвращает многомерный массив стуктурой, которой мы задавали в setName() филда.

Пример:
```php
if ($this->getHandlerRules()['shop']['offers']['offer']['@offer_data_source'] == 'CUSTOM') {
        $arFields = array_merge($arFields, $this->getOfferDefaultFields());
    }
```
---
##### addToTmpExportFile()
Метод дописывает переданную строку во временный файл экспорта, который в случае успешного формировании импорта заменяется на постоянный.

Иного способа записывать экспоотрый файл нет и быть не может.

---
##### getExportFileFormat()
Метод должен вернуть формат экспортного файла. Возвращается без точки.

---
##### executeMakeExportFile()
Входной метод хэндлера по созданию экспортного файла. В этом методе описывается создания файла целиком.

**Важный момент!!!** - когда дело доходит до перебора товаров, необходимо вызвать **$this->beginFilterProduct($obResult)**. Этот метод описан в абстратном классе, запускает механизм извлечения, перебора и фильтрации товаров. В конечном итоге, если товар прошел фильтр, абстрактный класс вызывает **beginOfferForeachBody()**.

---
##### beginOfferForeachBody()

В этом методе необходимо описать формирование тело товара по полученным данным, который в случае успеха попадает в экспортный файл.

**Важные моменты:**

**.1** В этом методе необходимо обновлять данные лога о товарах. Логика проста:
```php
// Начало метода
$arLog = $obResult->getData();
$arLog['PRODUCTS_TOTAL']++;

// Далее идет формирование тела товара, несколько проверок, и в конечном итоге
//  , если данные по товару соответствуют нашим ожиданиям, дополняем лог
$arLog['PRODUCTS_EXPORTED']++;
    
// Конец метода
$obResult->setData($arLog);
```
**.2** За извлечение конечных значений выступает **extractFilledValueFromRule()**. Стоит понимать, что значения не все фидов являются конечными. К примему InputText имеет конечное значение, но Resource имеет только описания правил для извлечения значений. К тому же занчение того же самого филда InputText требует пост обработки (к примеру trim()). 

Поэтому **ВСЕ** значения **должны** извлекаться с помощью метода **extractFilledValueFromRule()**. Пример использования:
```php
$arOfferXml['_attributes']['id'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__id'], $arExportProductData);

if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__group_id'], $arExportProductData))) {
    $arOfferXml['_attributes']['group_id'] = $this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__group_id'], $arExportProductData);
}

if (!is_null($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__available'], $arExportProductData))) {
    $arOfferXml['_attributes']['available'] = ($this->extractFilledValueFromRule($this->getFields()['shop__offers__offer__@attr__available'], $arExportProductData) == 'Y') ? 'true' : 'false';
}
```
**ВНИМАНИЕ!!** Извлеченные значения находятся в первоначальном виде. Для передачи их в XML или т.д. их необходимо обработать (htmlspecialchars к примеру). Сделано это для удобства сравнения и поиска вхождения. Ну и в принципе логично.

**.3** Если экспортируемый файл в конечном итоге будет представлять собой XML, то мы можем воспользоваться методом **convertArrayToString()**, который является оберткой библиотеки **spatie/array-to-xml**. Достаточно подготовить массив с необходимой ему структурой и передать в **convertArrayToString()**.

**ВНИМАНИЕ!!** Т.к. библиотека сама прогоняет значения через htmlspecialchars, то нет нужды прогонять извлеченные значения филдов через htmlspecialchars самому при формировании массива.

Пример из ЯМаркета:
```php
$this->addToTmpExportFile( $this->convertArrayToString($arOfferXml, 'offer') );
```
