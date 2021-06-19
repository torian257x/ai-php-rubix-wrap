<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\UtilityService;

class SortTest extends \PHPUnit\Framework\TestCase
{


  public function testCanSort()
  {
    $x = ['a' => 1, 'b' => 2, 'c' => 3];

    $z = new \ArrayObject([$x, $x, $x]);

    $z = UtilityService::sortToEnd($z, 'a');

    $array = $z->getArrayCopy();
    $first_row = $array[0];

    $a_val = array_pop($first_row);
    self::assertEquals($a_val, 1);

  }
}
