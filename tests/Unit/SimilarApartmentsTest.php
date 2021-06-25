<?php


namespace Torian257x\RubWrap\Tests\Unit;


use Torian257x\RubWrap\Service\RubixService;
use Torian257x\RubWrap\Service\UtilityService;
use Rubix\ML\Classifiers\KDNeighbors;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Extractors\ColumnPicker;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\Kernels\Distance\Canberra;
use Rubix\ML\Kernels\Distance\Cosine;
use Rubix\ML\Kernels\Distance\Diagonal;
use Rubix\ML\Kernels\Distance\Gower;
use Rubix\ML\Kernels\Distance\Jaccard;
use Rubix\ML\Kernels\Distance\Manhattan;
use Rubix\ML\Kernels\Distance\Minkowski;
use Rubix\ML\Loggers\Screen;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Pipeline;
use Rubix\ML\Transformers\IntervalDiscretizer;
use Rubix\ML\Transformers\MaxAbsoluteScaler;
use Rubix\ML\Transformers\MinMaxNormalizer;
use Rubix\ML\Transformers\MissingDataImputer;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Transformers\ZScaleStandardizer;

class SimilarApartmentsTest extends \PHPUnit\Framework\TestCase
{

  public function testGetSimilar()
  {
    $data      = new ColumnPicker(
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



    $data      = iterator_to_array($data->getIterator());
    $data      = array_slice($data, 0, 1000);

    $data2      = $data;

    for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
      $data[$i]['rr'] = $data[$i]['rooms'] * $data[$i]['rooms'];
      $data[$i]['rrr'] = $data[$i]['rooms'] * $data[$i]['rooms'] * $data[$i]['rooms'];
      $data[$i]['pp'] = $data[$i]['price_millions'] * $data[$i]['price_millions'];
      $data[$i]['ppp'] = $data[$i]['price_millions'] * $data[$i]['price_millions'] * $data[$i]['price_millions'];
      $data[$i]['p_t_r'] = $data[$i]['price_millions'] * $data[$i]['rooms'] ;
      $data[$i]['p_t_rr'] = $data[$i]['price_millions'] * $data[$i]['rooms'] * $data[$i]['rooms'];
      $data[$i]['pp_t_rr'] = $data[$i]['price_millions'] * $data[$i]['price_millions']  * $data[$i]['rooms'] * $data[$i]['rooms'];
      $data[$i]['ppp_t_rrr'] = $data[$i]['price_millions'] * $data[$i]['price_millions'] * $data[$i]['price_millions']  * $data[$i]['rooms'] * $data[$i]['rooms'] * $data[$i]['rooms'];
//      unset($data[$i]['zone_2_id']);
    }



    $nr_groups = ceil(sqrt(count($data)/2));


    RubixService::train(
        $data,
        data_index_w_label: null,
        estimator_algorithm: new KMeans($nr_groups, kernel: new Gower(1)),
        transformers: [
            new MissingDataImputer(),
            new NumericStringConverter(),
            new OneHotEncoder(),
            new MinMaxNormalizer(0,1),
//            new ZScaleStandardizer(),
        ]
    );

//    $needs_similar_data = ["1.4400", "80", "2", "Belen", "Belén La Palma", "1", "Gas", "24 Horas", "5", "1", "1", "1"];
//    $needs_similar_data = [["1.3000","35","1","El Poblado","El Poblado","0","No tiene","24 Horas","5","1","0","1"]];
    $needs_similar_data = ["1.3000", "85", "3", "Laureles", "Calasanz", "1", "Gas", "24 Horas", "4", "1", "0", "1"];

//    $similars = RubixService::predict($needs_similar_data);
    $similars = RubixService::predict($data);

    for ($i = 0, $iMax = count($data); $i < $iMax; $i++) {
      $data[$i]['cluster'] = $similars[$i];
      $data[$i]['zone_2_id2222'] = $data2[$i]['zone_2_id'];
    }


    usort(
        $data,
        function ($a, $b) {
          return $a['cluster'] <=> $b['cluster'];
        }
    );

    $csv = new CSV(__DIR__ . '/text.csv', true);

    $csv->export(new \ArrayObject($data));

//    var_dump($data);

  }


}
