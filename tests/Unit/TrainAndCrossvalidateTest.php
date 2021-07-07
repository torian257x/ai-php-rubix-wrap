<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\RubixService;

class TrainAndCrossvalidateTest extends \PHPUnit\Framework\TestCase
{

  public function testConfig()
  {


    $data = IrisTrainTest::getIrisData();

    $report = RubixService::train($data, 'iris_plant_type');

    self::assertGreaterThan(0.5, $report['informedness']);

  }


}
