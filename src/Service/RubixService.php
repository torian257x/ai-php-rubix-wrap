<?php


namespace Torian257x\RubWrap\Service;


use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\CrossValidation\Metrics\FBeta;
use Rubix\ML\CrossValidation\Metrics\Informedness;
use Rubix\ML\CrossValidation\Metrics\MCC;
use Rubix\ML\CrossValidation\Reports\ErrorAnalysis;
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

    $logger->info('Starting to train');


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

    $estimator->train($dataset);

    $estimator->save();
    $logger->info('Finished training');

    return $estimator->trained();
  }


  /**
   * @param array[] $input_data 2 dimensional array WIHTOUT label (e.g. without the value you want to predict)
   * @return mixed[]
   */
  public static function predict(array $input_data): array
  {
    $input_data = new Unlabeled($input_data);

    $estimator = PersistentModel::load(new Filesystem(self::MODEL_PATH));

    $prediction = $estimator->predict($input_data);

    return $prediction;
  }


  public static function getErrorAnalysis(array $samples_w_labels, $key_for_labels){

    [$samples, $labels] = UtilityService::getLabelsFromSamples($samples_w_labels, $key_for_labels);


    $logger = new Screen('ErrorAnalysis');

    $dataset = new Unlabeled($samples);



    $estimator = PersistentModel::load(new Filesystem(self::MODEL_PATH));

    $logger->info('Starting Error Analysis');

    $predictions = $estimator->predict($dataset);


    if(is_numeric($predictions[0])){
      $report = new ErrorAnalysis();
      $results = $report->generate($predictions, $labels);
    }else{
      $metric = new FBeta(0.7);
      $fbeta = $metric->score($predictions, $labels);

      $metric = new MCC();
      $mcc = $metric->score($predictions, $labels);

      $metric = new Informedness();
      $informedness = $metric->score($predictions, $labels);


      $results = compact('fbeta', 'mcc', 'informedness');
    }

    return $results;

  }
}
