<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\RubixService;
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

  public function testGetSimilar()
  {
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
    $data = array_slice($data, 0, 1000);

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


    for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
      $data[$i]['rr'] = $data[$i]['rooms']^2;
      $data[$i]['pp'] = $data[$i]['price_millions']^2;
      $data[$i]['p_t_r'] = $data[$i]['price_millions'] * $data[$i]['rooms'] ;
      $data[$i]['p_t_lat'] = $data[$i]['price_millions'] * $data[$i]['geo_lat'] ;
      $data[$i]['p_t_lng'] = $data[$i]['price_millions'] * $data[$i]['geo_lng'] ;
    }


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

  }


}
