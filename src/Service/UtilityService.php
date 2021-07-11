<?php


namespace Torian257x\RubWrap\Service;


use PhpParser\Node\Expr\Instanceof_;
use Rubix\ML\Estimator;
use Rubix\ML\EstimatorType;

class UtilityService
{
  const CLASSIFIER_SUPERVISED = 'classifier_supervised';
  const CLUSTERER             = 'clusterer';
  const REGRESSOR             = 'regressor';
  const ANOMALITY             = 'anomality';

  /**
   * @param array<array> $array
   * @return array<array>
   */
  public static function getLabelsFromSamples(array $data, $key_of_label): array
  {

    $labels = [];

    foreach ($data as $key => &$row) {
      $labels[] = $row[$key_of_label];
      unset($row[$key_of_label]);
    }

    return [$data, $labels];
  }


  /**
   * @return ?string 'classifier_supervised', 'clusterer', 'regressor' or null if type not found.
   * classifier_supervised: find group with labeled samples e.g. cat and dog photos, define which one is which.
   * clusterer: find groups with unlabeled samples e.g. given 100 apartments, divide the apartments into groups that are similar to each other (space, rooms, cost etc)
   * regressor: given data, find a value. E.g. given an apartment with number of rooms, space, year it was built, location etc find out the price.
   * anomality: given data, find values that are not normal
   */
  public static function getEstimatorType(Estimator $estimator)
  {

    $type = $estimator->type()->code();
    if($type === EstimatorType::CLASSIFIER){
      return self::CLASSIFIER_SUPERVISED;
    } else if ($type === EstimatorType::CLUSTERER){
      return self::CLUSTERER;
    } else if ($type === EstimatorType::REGRESSOR){
      return self::REGRESSOR;
    } else if ($type === EstimatorType::ANOMALY_DETECTOR){
      return self::ANOMALITY;
    } else{
      return null;
    }
  }



  public static function getRowsFromMultiDimArray(array $array, $key, $search_for_value)
  {
    return array_filter($array, function(array $val, $k) use($key, $search_for_value){
      return $val[$key] === $search_for_value;
    }, mode: ARRAY_FILTER_USE_BOTH);
  }


  public static function createIfNotExistsFolder(string $path)
  {
    if (is_dir($path)) {
        return;
    }

    if (!mkdir($path, 0777, true) && !is_dir($path)) {
        throw new \RuntimeException(sprintf('RubixAI Error: Directory "%s" was not created', $path));
    }

  }

}
