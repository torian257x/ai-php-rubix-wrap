<?php


namespace Torian257x\RubWrap\Service;


use Rubix\ML\Classifiers\AdaBoost;
use Rubix\ML\Classifiers\ClassificationTree;
use Rubix\ML\Classifiers\DummyClassifier;
use Rubix\ML\Classifiers\ExtraTreeClassifier;
use Rubix\ML\Classifiers\GaussianNB;
use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Classifiers\LogisticRegression;
use Rubix\ML\Classifiers\MultilayerPerceptron;
use Rubix\ML\Classifiers\NaiveBayes;
use Rubix\ML\Classifiers\RadiusNeighbors;
use Rubix\ML\Classifiers\RandomForest;
use Rubix\ML\Classifiers\SoftmaxClassifier;
use Rubix\ML\Classifiers\SVC;
use Rubix\ML\Clusterers\DBSCAN;
use Rubix\ML\Clusterers\FuzzyCMeans;
use Rubix\ML\Clusterers\GaussianMixture;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Clusterers\MeanShift;
use Rubix\ML\Estimator;
use Rubix\ML\Regressors\Adaline;
use Rubix\ML\Regressors\DummyRegressor;
use Rubix\ML\Regressors\ExtraTreeRegressor;
use Rubix\ML\Regressors\GradientBoost;
use Rubix\ML\Regressors\KDNeighborsRegressor;
use Rubix\ML\Regressors\KNNRegressor;
use Rubix\ML\Regressors\MLPRegressor;
use Rubix\ML\Regressors\RadiusNeighborsRegressor;
use Rubix\ML\Regressors\RegressionTree;
use Rubix\ML\Regressors\Ridge;
use Rubix\ML\Regressors\SVR;

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
    $classifier_supervised = [
        AdaBoost::class,
        ClassificationTree::class,
        DummyClassifier::class,
        ExtraTreeClassifier::class,
        GaussianNB::class,
        KDNeighbors::class,
        KNearestNeighbors::class,
        LogisticRegression::class,
        MultilayerPerceptron::class,
        NaiveBayes::class,
        RadiusNeighbors::class,
        RandomForest::class,
        SoftmaxClassifier::class,
        SVC::class,
    ];

    $clusterers = [
        DBSCAN::class,
        FuzzyCMeans::class,
        GaussianMixture::class,
        KMeans::class,
        MeanShift::class,
    ];

    $regressors = [
        Adaline::class,
        DummyRegressor::class,
        ExtraTreeRegressor::class,
        GradientBoost::class,
        KDNeighborsRegressor::class,
        KNNRegressor::class,
        MLPRegressor::class,
        RadiusNeighborsRegressor::class,
        RegressionTree::class,
        Ridge::class,
        SVR::class,

    ];

    if(in_array($estimator, $classifier_supervised)){
      return 'classifier_supervised';
    } else if (in_array($estimator, $clusterers)){
      return 'clusterer';
    } else if (in_array($estimator, $regressors)){
      return 'regressor';
    } else{
      return null;
    }
  }

}
