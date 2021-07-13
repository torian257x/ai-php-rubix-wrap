<?php


namespace Torian257x\RubWrap\Service\ResultFillers;


use Rubix\ML\Estimator;

interface ResultFiller
{
  public static function predict(array $data, Estimator $estimator): array;
}
