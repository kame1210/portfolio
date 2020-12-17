<?php

namespace portfolio;

date_default_timezone_set('Asia/Tokyo');

require_once dirname(__FILE__) . './../vendor/autoload.php';

class Bootstrap
{
  const TEMPLATE_DIR = self::APP_DIR . 'templates/portfolio/';

  const CACHE_DIR = false;

  const APP_URL = 'https://still-forest-34153.herokuapp.com/';

  const ENTRY_URL = self::APP_URL . 'php/';

  public static function loadClass($class)
  {
    $path = str_replace('\\', '/', self::APP_DIR . $class . '.class.php');
    require_once $path;
  }
}

spl_autoload_register([
  'portfolio\Bootstrap',
  'loadClass'
]);
