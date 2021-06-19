<?php


namespace Torian257x\RubWrap\Service;


use iterable;
use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Estimator;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Loggers\Screen;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Pipeline;
use Rubix\ML\Transformers\MissingDataImputer;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Transformers\PolynomialExpander;
use Rubix\ML\Transformers\Transformer;

class RubixService
{


  /**
   * @param iterable $data make array iterable by doing $myiterable = new ArrayObject( ['a','b'] );
   * @param Estimator|null $estimator_algorithm
   * @param Transformer[]|null $transformers
   * @param mixed $data_index_w_label the number/string of the index of the data to be trained
   */
  public static function train(
      iterable $data,
      $data_index_w_label,
      Estimator $estimator_algorithm = null,
      array $transformers = null
  ) {
    ini_set('memory_limit', '-1');


    uksort($data, function($a , $b){

    });

    $logger = new Screen("TrainData");


    $dataset = Labeled::fromIterator($data);

    if (is_null($estimator_algorithm)) {
      $estimator_algorithm = new KDNeighbors();
    }

    if (is_null($transformers)) {
      $transformers = [
          new NumericStringConverter(),
          new MissingDataImputer(),
          //                new OneHotEncoder(),
      ];
    }

    $estimator = new PersistentModel(
        new Pipeline(
            $transformers
            , $estimator_algorithm
        ),
        new Filesystem(__DIR__ . '/ai_output/model_trained.rbx')
    );


    $estimator->setLogger($logger);

    $estimator->train($dataset);

    $extractor = new CSV(__DIR__ . '/ai_output/progress.csv', true);

    $extractor->export($estimator->steps());

    $logger->info('Progress saved to progress.csv');

    $estimator->save();
  }
}
