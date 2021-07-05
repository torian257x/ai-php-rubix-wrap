<?php


namespace Torian257\RubWrap\Tests\Unit;


use Torian257\RubWrap\Service\RubixService;
use Rubix\ML\AnomalyDetectors\GaussianMLE;
use Rubix\ML\AnomalyDetectors\IsolationForest;
use Rubix\ML\AnomalyDetectors\Loda;
use Rubix\ML\AnomalyDetectors\OneClassSVM;
use Rubix\ML\AnomalyDetectors\RobustZScore;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Extractors\CSV;

class AnomalyTest extends \PHPUnit\Framework\TestCase
{

  public function testCanFindAnomalies()
  {

    $data = SimilarApartmentsTest::getData();


    $estimator = new GaussianMLE(contamination: 0.005);

    $data = RubixService::train($data, null, $estimator);

    $data = array_filter($data, function($row){
      return $row['anomaly'];
    });


    $anomaly_col = array_column($data, 'anomaly_score');
    $sum = array_sum($anomaly_col);
    self::assertGreaterThan(1, ($sum));
    RubixService::toCsv($data, 'anomalies.csv');


  }

}