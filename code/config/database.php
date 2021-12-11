<?php 
return array (
  'default' => 'mysql',
  'time_query_rule' => 
  array (
  ),
  'auto_timestamp' => true,
  'datetime_format' => 'Y-m-d H:i:s',
  'datetime_field' => '',
  'connections' => 
  array (
    'mysql' => 
    array (
      'type' => 'mysql',
      'hostname' => '10.7.69.171',
      'username' => 'root',
      'password' => '123',
      'database' => 'test',
      'charset' => 'utf8',
      'prefix' => '',
      'debug' => true,
      'fields_strict' => false,
      'deploy' => 0,
      'rw_separate' => false,
      'master_num' => 1,
      'slave_no' => '',
      'break_reconnect' => false,
      'trigger_sql' => true,
      'fields_cache' => false,
    ),
    'kunlun' => 
    array (
      'type' => 'mysql',
      'hostname' => '127.0.0.1',
      'username' => 'qingscan',
      'password' => '123',
      'database' => 'kunlun',
      'charset' => 'utf8',
      'prefix' => '',
      'debug' => true,
      'fields_strict' => false,
      'deploy' => 0,
      'rw_separate' => false,
      'master_num' => 1,
      'slave_no' => '',
      'break_reconnect' => false,
      'trigger_sql' => true,
      'fields_cache' => false,
    ),
  ),
);
?>