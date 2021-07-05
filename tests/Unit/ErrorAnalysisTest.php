<?php


namespace Torian257\RubWrap\Tests\Unit;



use Torian257\RubWrap\Service\RubixService;
use PHPUnit\Framework\TestCase;
use Rubix\ML\Extractors\CSV;

class ErrorAnalysisTest extends TestCase
{

  public function testCanAnalyzeError()
  {

    $data = new CSV(__DIR__ . '/traininput/bezdekiris.csv', true);
    $data = iterator_to_array($data->getIterator());

    $report = RubixService::getErrorAnalysis($data, 'iris_plant_type');


    var_dump($report);
    self::assertTrue(!!$report);
  }

}
