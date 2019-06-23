<?
include_once __DIR__."/../local/php_interface/vendor/autoload.php";

$conf = [
    'composer' => [
        'value' => ['config_path' => realpath( __DIR__.'/../local/php_interface/composer.json' )]
    ],

    /**
     * Конфиг local.core
     */
    'local.core' => [
        'value' => [
            'xor_key' => 'B9=[$>XYB89=n;_a$y}?3`Tz]Q8$T*gn'
        ],
        'readonly' => true
    ],

    /**
     * Конфиг Dadata.ru.
     * Ключ зареган на info@weblooter.ru
     */
    'dadata' => [
        'value' => [
            'token' => '42e51e202146fbce11bb2c5a2b1f56afabb5a039',
            'secret' => '155be265b2fbdcb0dfe1804b20afb388628691ba',
            'url' => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/'
        ],
        'readonly' => true
    ],

    /**
     * Кофиг для работы с магазинами
     */
    'store' => [
        'value' => [
            'upload_xml' => [
                'max_size_mb' => 75 // Максимальный размер загружаемого файла
            ],
            'download_xml' => [
                'max_size_mb' => 300, // Максимальный размер скачиваемого файла в мегабайтах
                'connect_timeout' => 60 // Количество секунд ожидания при попытке скачивания
            ],
            'cleaner' => [
                'delete_deactivated_after_days' => 60, // Кол-во дней, после которого деактивированные магазины будут удалены
            ]
        ],
        'readonly' => true
    ],

    /**
     * Конфиг для работы с очередями
     */
    'job_queue' => [
        'value' => [
            'MAXIMUM_WORKERS' => 2, // Максимальное кол-во одновременно запущенных воркеров
            'MAX_CYCLES_COUNT' => -1, // Максимальное кол-во циклов. Если -1 - бесконечный воркер
            'TIME_BETWEEN_CYCLES' => 10, // Время в секундах между запускамициклов
            'WORKER_TIMEOUT' => 3600, // Timeout воркера
        ],
        'readonly' => true
    ],

    /**
     * Конфиг для работы Robofeed
     */
    'robofeed' => [
        'value' => [
            'XMLReader' => [
                'max_offers_error_count_in_validation' => 5, // Максимальное кол-во ошибок среди товаров в валидаторе, после которого валидация прекращается
            ],
            'ImportLogTable' => [
                'max_last_log_count' => 18, // Какое кол-во логов импорта хранить для магазина
            ],
            'convert' => [
                'upload_file_max_size' => 100, // Максимальный развер файла для конвертера в мб
                'delete_file_after' => 240, // Удалять файл после конвертирования через N минут
                'max_in_queue' => 1, // Максимальное кол-во файлов в очереди на обработку
            ],
            'import' => [
                'timeout_between_import_robofeed' => 240, // Интервал между актиуализацей товаров магазина в минутах
            ]
        ],
        'readonly' => true
    ],

    /**
     * Конфиг для почты
     */
    'mail' => [
        'value' => [
            'smtp' => [
                'host' => 'smtp.yandex.ru',
                'login' => 'info@robofeed.ru',
                'password' => '>4+QGny@T]y7cdh{',
                'name' => 'Robofeed.ru - Быстрая интеграция с торговыми площадками',
            ]
        ],
        'readonly' => true
    ],

    /**
     * Конфиг платежных систем
     */
    'payment' => [
        'value' => [
            'yandex-money' => [
                /**
                 * Мануал по настройке формы https://tech.yandex.ru/money/doc/payment-buttons/reference/forms-docpage/
                 */
                'receiver_id' => '410012393333506',
                'successURL' => 'https://robofeed.ru/personal/balance/top-up/?handler=yandex-money&result=success',
                /**
                 * Секретный ключ, а так же настройка пути - https://money.yandex.ru/myservices/online.xml
                 * Если ссылка не работает - читаем мануал https://tech.yandex.ru/money/doc/payment-buttons/reference/notifications-docpage/
                 * Сейчас путь бьет на https://robofeed.ru/personal/balance/top-up/?handler=yandex-money&result=success
                 */
                'secret_key' => 'YF/hsae7lMvXnARmvzooSxlL'
            ],
            'bill' => [
                'inn' => '505078003987',
                'kpp' => '-',
                'recipient' => 'ИП Черешнев Евгений Сергеевич',
                'rs' => '40802810770010147432',
                'bank' => 'МОСКОВСКИЙ ФИЛИАЛ АО КБ "МОДУЛЬБАНК"',
                'bik' => '044525092',
                'kr' => '30101810645250000092',

                'sing_1_link' => \Bitrix\Main\Application::getDocumentRoot().'/local/tools/payment/bill/sign200x50.png',
                'sing_2_link' => \Bitrix\Main\Application::getDocumentRoot().'/local/tools/payment/bill/sign200x50.png',
                'printing_link' => \Bitrix\Main\Application::getDocumentRoot().'/local/tools/payment/bill/printing150x150.png',
            ]
        ],
        'readonly' => true
    ],

    /**
     * Торговые компании и выгрузки
     */
    'tradingplatform' => [
        'value' => [
            'export' => [
                'export_dir' => '/upload/tradingplatform/export', // Директория хранения экспортных файлов
                'batch_size' => 50, // Размет батча при фильтрации товаров
                'max_logs_count' => 10, // Максимальное кол-во логов по ТП
                'timeout_between_export' => 180, // Интервал создания экспортного файла в минутах
            ]
        ],
        'readonly' => true
    ],

    /**
     * Курсы валют
     * www.currencyconverterapi.com
     */
    'currencyconverterapi' => [
        'value' => [
            'apikey' => '3bd89a05fe5c6539f84c'
        ],
        'readonly' => true
    ],

    /**
     * Рекаптча
     */
    'recaptcha' => [
        'value' => [
            'site_key' => '6LeHa6IUAAAAABnsTMSgj2oD-oTt1_8ygDlQ2KaQ',
            'secret_key' => '6LeHa6IUAAAAALWXwZqEgKXi1d-88-SCJM4JycFT'
        ],
        'readonly' => true
    ]
];

return $conf;