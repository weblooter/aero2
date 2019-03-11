<?
include_once __DIR__ . "/../local/php_interface/vendor/autoload.php";

$conf = [
    'composer' => [
        'value' => ['config_path' => realpath(__DIR__ . '/../local/php_interface/composer.json')]
    ],

    /**
     * Конфигурация Dadata.ru.
     * Ключ зареган на info@weblooter.ru
     */
    'dadata' => array(
        'value' => array(
            'token' => '42e51e202146fbce11bb2c5a2b1f56afabb5a039',
            'secret' => '155be265b2fbdcb0dfe1804b20afb388628691ba',
            'url' => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/'
        )
    ),

    /**
     * Конфигурации для работы со счетами.
     */
    'bill' => [
        'value' => [
            // Класс и метод, отвечающий за построение ACCOUNT_NUMBER
            'bill_account_number_constructor_class' => Local\Core\Inner\Bill\Base::class,
            'bill_account_number_constructor_method' => 'createAccountNumber'
        ]
    ]
];

return $conf;