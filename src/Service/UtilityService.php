<?php


namespace Torian257x\RubWrap\Service;


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

}
