<?php


namespace Torian257x\RubWrap\Service\ResultFillers;


use Torian257x\RubWrap\Service\RubixService;
use Rubix\ML\AnomalyDetectors\Scoring;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Estimator;

class AnomalyFiller implements ResultFiller
{

  public static function predict(array $data, Estimator $estimator): array
  {
    $anomalies = RubixService::predict($data);


    if($estimator instanceof Scoring){
      $scores = $estimator->score(Unlabeled::build($data));
    }

    $can_score = $estimator instanceof Scoring;

    for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
      $data[$i]['anomaly'] = $anomalies[$i];

      if ($can_score && $scores ?? null) {
        $data[$i]['anomaly_score'] = $scores[$i];
      }

    }

   if($can_score){
    usort(
        $data,
        function ($a, $b) {
          return $b['anomaly_score'] <=> $a['anomaly_score'];
        }
    );
   } else{
    usort(
        $data,
        function ($a, $b) {
          return $a['anomaly'] <=> $b['anomaly'];
        }
    );
   }

    return $data;

  }
}
