<?php


namespace Torian257\RubWrap\Tests\Unit;



use Torian257\RubWrap\Service\RubixService;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

class PredictTest extends \PHPUnit\Framework\TestCase
{

  public function testCanPredict()
  {

    $data = [
        'virginica' => ['sepal_length_cm' => "5.9", 'sepal_width_cm' => "3", 'petal_length_cm' => "5.1", 'petal_width_cm' => "1.8"],
        'versicolor' => ['sepal_length_cm' => "6.8", 'sepal_width_cm' => '2.8', 'petal_length_cm' => "4.8", 'petal_width_cm' => "1.4"],
        'setosa' => ['sepal_length_cm' => "5.1", 'sepal_width_cm' => '3.8', 'petal_length_cm' => "1.6", 'petal_width_cm' => "0.2"],
        ];

    $predictions = RubixService::predict(array_values($data));

    self::assertEquals(array_keys($data), $predictions);

  }

}
