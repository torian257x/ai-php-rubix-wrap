<?php

if(!function_exists('rubixai_getconfig')){
  function rubixai_getconfig(string $config_entry = null){
    $config = require(__DIR__ . '/rubwrap_config.php');

    if ($config_entry) {
      return $config[$config_entry] ?? null;
    }

    return $config;

  }
}


