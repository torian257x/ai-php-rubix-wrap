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

class TrainTest extends \PHPUnit\Framework\TestCase
{

  public function testCanTrain()
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

    RubixService::train($data, 'iris_plant_type');

  }


}
