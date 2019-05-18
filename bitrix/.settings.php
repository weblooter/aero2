<?php
$arConnection = require($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/connection_data.php');
$arConnection = $arConnection[ ( !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : end(array_diff(explode('/', $_SERVER['DOCUMENT_ROOT']), [''])) ) ];

return array (
  'utf_mode' => 
  array (
    'value' => true,
    'readonly' => true,
  ),
  'cache_flags' => 
  array (
    'value' => 
    array (
      'config_options' => 3600,
      'site_domain' => 3600,
    ),
    'readonly' => false,
  ),
  'cookies' => 
  array (
    'value' => 
    array (
      'secure' => false,
      'http_only' => true,
    ),
    'readonly' => false,
  ),
  'exception_handling' => 
  array (
    'value' => 
    array (
      'debug' => true,
      'handled_errors_types' => 4437,
      'exception_errors_types' => 4437,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => 256,
      'log' => 
      array (
        'settings' => 
        array (
          'file' => '/var/log/php/exceptions.log',
          'log_size' => 1000000,
        ),
      ),
    ),
    'readonly' => false,
  ),
  'crypto' => 
  array (
    'value' => 
    array (
      'crypto_key' => '2c634f84b9072b990fef073bb1df9c30',
    ),
    'readonly' => true,
  ),
  'connections' => 
  array (
    'value' => 
    array (
      'default' => 
      array (
        'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
        'host' => $arConnection['host'],
        'database' => $arConnection['dbname'],
        'login' => $arConnection['username'],
        'password' => $arConnection['password'],
        'options' => 2,
      ),
    ),
    'readonly' => true,
  ),
);
