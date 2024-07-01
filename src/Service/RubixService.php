<?php


namespace Torian257x\RubWrap\Service;


use Exception;
use Rubix\ML\DataType;
use Rubix\ML\Regressors\KDNeighborsRegressor;
use Rubix\ML\Transformers\MinMaxNormalizer;
use Rubix\ML\Transformers\OneHotEncoder;
use Torian257x\RubWrap\Exception\RubWrapException;
use Torian257x\RubWrap\Service\ResultFillers\AnomalyFiller;
use Torian257x\RubWrap\Service\ResultFillers\ClustererFiller;
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
use Rubix\ML\Transformers\Transformer;

class RubixService
{

    public static function train(
        array $data,
        mixed $data_index_w_label = null,
        Estimator $estimator_algorithm = null,
        array $transformers = null,
        $model_filename = 'model_trained.rbx',
        float $train_part_size = 0.7
    ) {

        $is_testable = self::is_testable($estimator_algorithm);

        if ($is_testable) {
            $data_size = sizeof($data);

            if (!$data || !$data_size) {
                throw new RubWrapException('Invalid $data provided');
            }

            shuffle($data);

            $train_size = ceil($data_size * $train_part_size);

            $train_data = array_slice($data, 0, $train_size);
            $test_data = array_slice($data, $train_size, sizeof($data) - 1);

            static::trainWithoutTest($train_data,
                $data_index_w_label,
                $estimator_algorithm,
                $transformers,
                $model_filename);

            return static::getErrorAnalysis($test_data, $data_index_w_label, $model_filename);
        }

        //clusterer or anomaly estimator, returning data that is enriched with scores, clusters, and anomality rating
        return static::trainWithoutTest(
            $data,
            $data_index_w_label,
            $estimator_algorithm,
            $transformers,
            $model_filename
        );
    }

    /**
     * @param array<array> $data make array iterable by doing $myiterable = new ArrayObject( ['a','b'] );
     * @param Transformer[]|null $transformers
     * @param mixed $data_index_w_label the number/string of the index of the data to be trained
     * @return bool|array[] typically boolean whether the training was successful, otherwise in case of cluster returns $data with extra entry 'cluster_nr'
     */
    public static function trainWithoutTest(
        array $data,
        mixed $data_index_w_label = null,
        ?Estimator $estimator_algorithm = null,
        array $transformers = null,
        $model_filename = 'model_trained.rbx'
    ) {
        ini_set('memory_limit', '-1');

        $logger = new Screen("TrainData");
        $logger->info('Starting to train');

        if ($data_index_w_label) {
            [$samples, $labels] = UtilityService::getLabelsFromSamples($data, $data_index_w_label);
            $dataset = new Labeled($samples, $labels);
            $estimator_algorithm = self::getEstimatorAlgorithm($estimator_algorithm, $labels[0]);
        } else {
            $dataset = new Unlabeled($data);
        }

        $transformers = self::getTransformers($transformers, $dataset);


        $output_path = rubixai_getconfig()['ai_model_path_output'];
        UtilityService::createIfNotExistsFolder($output_path);

        $estimator = new PersistentModel(
            new Pipeline(
                $transformers
                , $estimator_algorithm
            ),
            new Filesystem($output_path . $model_filename)
        );

        $estimator->train($dataset);

        $estimator->save();
        $logger->info('Finished training');

        $estimatorType = UtilityService::getEstimatorType($estimator);
        if ($estimatorType === UtilityService::CLUSTERER) {
            return ClustererFiller::predict($data, $estimator);
        } elseif ($estimatorType === UtilityService::ANOMALITY) {
            return AnomalyFiller::predict($data, $estimator);
        }

        return $estimator->trained();
    }


    /**
     * @param array[] $input_data 2 dimensional array WIHTOUT label (e.g. without the value you want to predict)
     * @return mixed[]
     */
    public static function predict(
        array $input_data,
        Estimator $estimator = null,
        string $model_filename = 'model_trained.rbx'
    ): array|int {

        $is_single_dimensional_array = false;
        if (is_array($input_data) && !is_array($input_data[0] ?? null)) {
            $input_data = [$input_data];
            $is_single_dimensional_array = true;
        }

        $logger = new Screen("Predict Data");

        $logger->info('Starting prediction');

        $input_data = new Unlabeled($input_data);

        if (is_null($estimator)) {
            $estimator = static::getEstimatorFromFilesystem($model_filename);
        }

        $prediction = $estimator->predict($input_data);

        if ($is_single_dimensional_array && is_array($prediction)) {
            return $prediction[0];
        } else {
            return $prediction;
        }
    }

    public static function getErrorAnalysis(
        array $samples_w_labels,
        $key_for_labels,
        $model_filename = 'model_trained.rbx'
    ) {

        [$samples, $labels] = UtilityService::getLabelsFromSamples($samples_w_labels, $key_for_labels);


        $logger = new Screen('ErrorAnalysis');

        $dataset = new Unlabeled($samples);

        $estimator = static::getEstimatorFromFilesystem($model_filename);

        $logger->info('Starting Error Analysis');

        $predictions = $estimator->predict($dataset);


        if (is_numeric($predictions[0])) {
            $report = new ErrorAnalysis();
            $results = $report->generate($predictions, $labels);
        } else {
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

    public static function getEstimatorFromFilesystem(string $model_filename = 'model_trained.rbx'): Estimator
    {
        return PersistentModel::load(new Filesystem(rubixai_getconfig('ai_model_path_output') . $model_filename));
    }

    public static function fromCsv(string $filename, ?array $columns = null)
    {
        if (!$filename) {
            throw new Exception('Filename cannot be null or empty or fasly');
        }

        if (is_array($columns)) {

            $data = new ColumnPicker(
                new CSV(rubixai_getconfig('csv_path_input'), true),
                $columns
            );

        } else {
            $data = new CSV(rubixai_getconfig('csv_path_input'), true);
        }

        return iterator_to_array($data);
    }


    public static function toCsv(array $data, string $filename)
    {
        if (!$filename) {
            throw new Exception('Filename cannot be null or empty or fasly');
        }

        $path = rubixai_getconfig("csv_path_output");
        UtilityService::createIfNotExistsFolder($path);
        $csv = new CSV($path . $filename, true);
        $csv->export(new \ArrayObject($data));
    }

    private static function is_testable(?Estimator $estimator_algorithm): bool
    {
        if (is_null($estimator_algorithm)) {
            return true;
        }

        $estimator_algorithm_type = $estimator_algorithm->type();
        if ($estimator_algorithm_type->isRegressor() || $estimator_algorithm_type->isClassifier()) {
            return true;
        }

        return false;
    }

    private static function getEstimatorAlgorithm(?Estimator $estimator_algorithm, mixed $label0): Estimator
    {
        if ($estimator_algorithm) {
            return $estimator_algorithm;
        }

        $data_type = DataType::detect($label0);

        if ($data_type->isContinuous()) { // needs_regression
            return new KDNeighborsRegressor();
        }
        return new KDNeighbors();
    }

    private static function getTransformers(?array $transformers, Unlabeled|Labeled $dataset): array
    {
        if ($transformers) {
            return $transformers;
        }

        $samples = $dataset->samples();
        $row1 = $samples[0];

        foreach ($row1 as $feat) {

            if (DataType::detect($feat)->isCategorical()) {
                return [
                    new NumericStringConverter(),
                    new MissingDataImputer(),
                    new OneHotEncoder(),
                    new MinMaxNormalizer(),
                ];
            }
        }

        return [
            new NumericStringConverter(),
            new MissingDataImputer(),
            new MinMaxNormalizer(),
        ];
    }

}
