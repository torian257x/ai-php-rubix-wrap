<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\RubixService;

class ReadmeExamplesTest extends \PHPUnit\Framework\TestCase
{


  public function testReadmeApartmentsExample()
  {

    $apartment_data = [
        ['space_m2' => 10, 'price' => 100],
        ['space_m2' => 20, 'price' => 200],
        ['space_m2' => 30, 'price' => 300],
        ['space_m2' => 40, 'price' => 400],
        ['space_m2' => 50, 'price' => 500],
        ['space_m2' => 60, 'price' => 600],
        ['space_m2' => 70, 'price' => 700],
        ['space_m2' => 80, 'price' => 800],
        ['space_m2' => 90, 'price' => 900],
        ['space_m2' => 100, 'price' => 1000],
        ['space_m2' => 110, 'price' => 1100],
        ['space_m2' => 120, 'price' => 1200],
        ['space_m2' => 130, 'price' => 1300],
        ['space_m2' => 140, 'price' => 1400],
        ['space_m2' => 150, 'price' => 1500],
        ['space_m2' => 160, 'price' => 1600],
        ['space_m2' => 170, 'price' => 1700],
        ['space_m2' => 180, 'price' => 1800],
        ['space_m2' => 190, 'price' => 1900],
        ['space_m2' => 200, 'price' => 2000],
        ['space_m2' => 210, 'price' => 2100],
        ['space_m2' => 220, 'price' => 2200],
        ['space_m2' => 230, 'price' => 2300],
        ['space_m2' => 240, 'price' => 2400],
        ['space_m2' => 250, 'price' => 2500],
        ['space_m2' => 260, 'price' => 2600],
        ['space_m2' => 270, 'price' => 2700],
        ['space_m2' => 280, 'price' => 2800],
        ['space_m2' => 290, 'price' => 2900],
        ['space_m2' => 300, 'price' => 3000],
    ];


    $report = RubixService::train($apartment_data, 'price',);

    self::assertGreaterThan(0.9, $report['r squared']);


    $prediction = RubixService::predict(['space_m2' => 250]);

    self::assertLessThan(0.1 * 2500, abs($prediction - 2500));

  }


}
