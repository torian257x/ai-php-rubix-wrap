<?php


namespace Torian257x\RubWrap\Service;




class UtilityService
{

  /**
   * @param iterable<array> $iterable
   * @return iterable<array>
   */
  public static function sortToEnd(iterable $iterable, $key_sort_to_end): iterable{

    foreach($iterable as &$row){
      $t = $row[$key_sort_to_end];
      unset($row[$key_sort_to_end]);
      $row[$key_sort_to_end] = $t;
    }

    return $iterable;
  }

}
