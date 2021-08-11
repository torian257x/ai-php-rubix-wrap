<?php

if (!function_exists('rubixai_getconfig')) {
  function rubixai_getconfig(string $config_entry = null)
  {

    if(defined('RUBIXAI_CUSTOM_CONFIG')){
      $config = \RUBIXAI_CUSTOM_CONFIG;
    }else{
      $config = require(__DIR__ . '/rubwrap_config.php');
    }

    if ($config_entry) {
      return $config[$config_entry] ?? null;
    }

    return $config;
  }
}
