<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\RubixService;
use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Loggers\Screen;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Pipeline;
use Rubix\ML\Regressors\Ridge;
use Rubix\ML\Transformers\MissingDataImputer;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Transformers\PolynomialExpander;

class IrisTrainTest extends \PHPUnit\Framework\TestCase
{
  /**
   * @return array
   */
  public static function getIrisData(): array
  {
    $data = new ColumnPicker(
        new CSV(__DIR__ . '/traininput/bezdekiris.csv', true), [
            'sepal_length_cm',
            'sepal_width_cm',
            'petal_length_cm',
            'petal_width_cm',
            'iris_plant_type',
        ]
    );

    $data = iterator_to_array($data->getIterator());
    return $data;
  }

  /**
   * @return array[]|bool
   */
  public static function trainIris(): mixed
  {
    $data       = self::getIrisData();

    $is_trained = RubixService::trainWithoutTest($data, 'iris_plant_type');

    return $is_trained;
  }

  public function testCanTrain()
  {

    $is_trained = self::trainIris();
    self::assertTrue($is_trained);

  }


}
