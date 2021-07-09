<?php


namespace Torian257x\RubWrap\Tests\Unit;



use Torian257x\RubWrap\Service\RubixService;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

class IrisPredictTest extends \PHPUnit\Framework\TestCase
{

  public function testCanPredict()
  {

    IrisTrainTest::trainIris();

    $data = [
          ['virginica', ["7.9","3.8","6.4","2"]], //he keys actually don't matter. The order is what matters
          ['virginica', ['sepal_length_cm' => "5.9", 'sepal_width_cm' => "3.2", 'petal_length_cm' => "6", 'petal_width_cm' => "1.8"]],
          ['virginica', ['sepal_length_cm' => "5.9", 'sepal_width_cm' => "3", 'petal_length_cm' => "5.1", 'petal_width_cm' => "1.8"]],
          ['versicolor', ['sepal_length_cm' => "6.8", 'sepal_width_cm' => '2.8', 'petal_length_cm' => "4.8", 'petal_width_cm' => "1.4"]],
          ['setosa', ['sepal_length_cm' => "5.1", 'sepal_width_cm' => '3.8', 'petal_length_cm' => "1.6", 'petal_width_cm' => "0.2"]],
        ];

    $labels = array_map(function($row){
      return $row[0];
    }, $data);

    $samples =  array_map(function($row){
      return $row[1];
    }, $data);

    $predictions = RubixService::predict($samples);

    self::assertEquals($labels, $predictions);

  }

}
