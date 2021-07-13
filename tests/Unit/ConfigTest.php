<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\RubixService;

class ConfigTest extends \PHPUnit\Framework\TestCase
{

  public function testConfig()
  {

    $config = rubixai_getconfig();

    self::assertNotEmpty($config['csv_path_output']);

  }

  public function testConfigParam(){

    $config_path = rubixai_getconfig('csv_path_output');
    self::assertNotEmpty($config_path);

    $config_shouldbenull = rubixai_getconfig('csv_path_outputasdfasdfasdfdasf');
    self::assertNull($config_shouldbenull);

    $rubixClass = rubixai_getconfig('RubixMainClass');
    self::assertTrue( $rubixClass === RubixService::class);

  }


}
