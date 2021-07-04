<?php


namespace Torian257x\RubWrap\Service\DataFillers;


use Rubix\ML\Estimator;

interface DataFiller
{
  public static function predict(array $data, Estimator $estimator): array;
}
