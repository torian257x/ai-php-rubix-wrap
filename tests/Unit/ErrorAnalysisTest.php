<?php


namespace Torian257x\RubWrap\Tests\Unit;



use Torian257x\RubWrap\Service\RubixService;
use PHPUnit\Framework\TestCase;
use Rubix\ML\Extractors\CSV;

class ErrorAnalysisTest extends TestCase
{

  public function testCanAnalyzeError()
  {

    IrisTrainTest::trainIris();

    $data = new CSV(__DIR__ . '/traininput/bezdekiris.csv', true);
    $data = iterator_to_array($data->getIterator());

    $report = RubixService::getErrorAnalysis($data, 'iris_plant_type');

    self::assertGreaterThan(0.9, $report['fbeta']);
  }

}
