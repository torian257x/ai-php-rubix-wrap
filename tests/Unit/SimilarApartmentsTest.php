<?php


namespace Torian257\RubWrap\Tests\Unit;


use Torian257\RubWrap\Service\RubixService;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Kernels\Distance\Manhattan;
use Rubix\ML\Transformers\MissingDataImputer;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Transformers\ZScaleStandardizer;

class SimilarApartmentsTest extends \PHPUnit\Framework\TestCase
{


  public static function getData(){
    $data = new ColumnPicker(
        new CSV(__DIR__ . '/traininput/apartments_1k.csv', true),
        [
            'price_millions',
            'space',
            'rooms',
            'geo_lat',
            'geo_lng',
            'parking',
            'water_heating',
            'doorman',
            'balcony',
        ]

    );

    $data = iterator_to_array($data->getIterator());
    $data = array_map(function($row){

      $wh = &$row['water_heating'];
      if($wh === 'No tiene'){
        $wh = 0;
      }else if($wh === 'Gas'){
        $wh = 1;
      }else if($wh === 'Eléctrico'){
        $wh = 0.5;
      }else{
        $wh = 0;
      }

      $dm = &$row['doorman'];
      if($dm === '24 Horas'){
        $dm = 1;
      }else if($dm === 'Diurna'){
        $dm = 0.5;
      }else if($dm === 'No tiene'){
        $dm = 0;
      }else{
        $dm = 0;
      }

      return $row;

    }, $data);


    foreach ($data as $i => $row) {
      $data[$i]['rr'] = $row['rooms']^2;
      $data[$i]['pp'] = $row['price_millions']^2;
      $data[$i]['p_t_r'] = $row['price_millions'] * $row['rooms'] ;
      $data[$i]['p_t_lat'] = $row['price_millions'] * $row['geo_lat'] ;
      $data[$i]['p_t_lng'] = $row['price_millions'] * $row['geo_lng'] ;
    }

    return $data;
  }

  public function testGetSimilar()
  {

    $data = self::getData();



    $data = array_slice($data, 0, 1000);


    $nr_groups = ceil(sqrt(count($data) / 2));


    $data_w_cluster_nr = RubixService::train(
        $data,
        data_index_w_label: null,
        estimator_algorithm: new KMeans($nr_groups, kernel: new Manhattan()),
        transformers: [
            new MissingDataImputer(),
            new NumericStringConverter(),
            new OneHotEncoder(),
            new ZScaleStandardizer(),
        ]
    );

    $csv = new CSV(__DIR__ . '/output/cluster_output.csv', true);
    $csv->export(new \ArrayObject($data_w_cluster_nr));

    $myclusters = array_column($data_w_cluster_nr, 'cluster_nr');
    self::assertGreaterThan(1, sizeof($myclusters));

  }


}
