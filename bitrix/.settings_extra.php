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
     * Конфиг для работы со счетами.
     */
    'bill' => [
        'value' => [
            // Класс и метод, отвечающий за построение ACCOUNT_NUMBER
            'bill_account_number_constructor_class' => Local\Core\Inner\Bill\Base::class,
            'bill_account_number_constructor_method' => 'createAccountNumber'
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
                'max_size_mb' => 300, // Максимальный размер скачиваемого файла
                'connect_timeout' => 180 // Количество секунд ожидания при попытке скачивания
            ]
        ],
        'readonly' => true
    ],

    /**
     * Конфиг для работы с очередями
     */
    'job_queue' => [
        'value' => [
            'MAXIMUM_WORKERS' => 2 // Максимальное кол-во одновременно запущенных воркеров
        ],
        'readonly' => true
    ],

    /**
     * Конфиг для работы Robofeed
     */
    'robofeed' => [
        'value' => [
            'XMLReader' => [
                'max_offers_error_count_in_validation' => 5
            ]
        ],
        'readonly' => true
    ]
];

return $conf;