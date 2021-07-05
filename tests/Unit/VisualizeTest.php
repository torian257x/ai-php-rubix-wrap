<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Kernels\Distance\Manhattan;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Transformers\PrincipalComponentAnalysis;
use Rubix\ML\Transformers\TSNE;

class VisualizeTest extends \PHPUnit\Framework\TestCase
{

  public function testCanReduce()
  {

    $data = new ColumnPicker(
        new CSV(__DIR__ . '/traininput/apartments_1k.csv', true),
        [
            'price_millions',
            'space',
            'rooms',
            'zone_2_id',
            'parking',
            'water_heating',
            'doorman',
            'balcony',
        ]

    );


    $dataset = Unlabeled::fromIterator($data->getIterator());


    $dataset->apply(new OneHotEncoder());
    $dataset->apply(new PrincipalComponentAnalysis(3));


    $csv = new CSV(__DIR__ . '/output/pca.csv', true);
    $csv->export($dataset->getIterator());

    self::assertEquals(3, sizeof($dataset->samples()[0]));

  }


}
