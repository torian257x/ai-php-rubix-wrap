<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\RubixService;

class ConfigTest extends \PHPUnit\Framework\TestCase
{

  public function testConfig()
  {

    $config = RubixService::getConfig();

    self::assertNotEmpty($config['csv_path_output']);

  }

  public function testConfigParam(){

    $config_path = RubixService::getConfig('csv_path_output');
    self::assertNotEmpty($config_path);

    $config_shouldbenull = RubixService::getConfig('csv_path_outputasdfasdfasdfdasf');
    self::assertNull($config_shouldbenull);

  }


}
