<?php


namespace Torian257x\RubWrap\Service;


use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
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
  const MODEL_PATH = __DIR__ . '/ai_output/model_trained.rbx';


  /**
   * @param array<array> $data make array iterable by doing $myiterable = new ArrayObject( ['a','b'] );
   * @param Estimator|null $estimator_algorithm
   * @param Transformer[]|null $transformers
   * @param mixed $data_index_w_label the number/string of the index of the data to be trained
   */
  public static function train(
      array $data,
      $data_index_w_label,
      Estimator $estimator_algorithm = null,
      array $transformers = null
  ) {
    ini_set('memory_limit', '-1');


    [$samples, $labels] = UtilityService::getLabelsFromSamples($data, $data_index_w_label);

    $logger = new Screen("TrainData");


    $dataset = new Labeled($samples, $labels);

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
        new Filesystem(self::MODEL_PATH)
    );


//    $estimator->setLogger($logger);

    $estimator->train($dataset);

//    $extractor = new CSV(__DIR__ . '/ai_output/progress.csv', true);

//    $extractor->export($estimator->steps());

//    $logger->info('Progress saved to progress.csv');

    $estimator->save();
    return $estimator->trained();
  }


  public static function predict(array $input_data)
  {
    $input_data = new Unlabeled($input_data);
    $estimator = PersistentModel::load(new Filesystem(self::MODEL_PATH));
    $prediction = $estimator->predict($input_data);
    return $prediction;
  }
}
