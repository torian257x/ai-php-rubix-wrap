<?php


namespace Torian257x\RubWrap\Service\ResultFillers;


use Torian257x\RubWrap\Service\RubixService;
use Rubix\ML\Estimator;

class ClustererFiller implements ResultFiller
{

  public static function predict(array $data, Estimator $estimator): array
  {
    $clusters = RubixService::predict($data);

    for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
      $data[$i]['cluster_nr'] = $clusters[$i];
    }

    usort(
        $data,
        function ($a, $b) {
          return $a['cluster_nr'] <=> $b['cluster_nr'];
        }
    );

    return $data;
  }
}
