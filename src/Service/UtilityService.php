<?php


namespace Torian257x\RubWrap\Service;


use PhpParser\Node\Expr\Instanceof_;
use Rubix\ML\Estimator;
use Rubix\ML\EstimatorType;

class UtilityService
{

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
   */
  public static function getEstimatorType(Estimator $estimator)
  {

    $type = $estimator->type()->code();
    if($type === EstimatorType::CLASSIFIER){
      return 'classifier_supervised';
    } else if ($type === EstimatorType::CLUSTERER){
      return 'clusterer';
    } else if ($type === EstimatorType::REGRESSOR){
      return 'regressor';
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

}
