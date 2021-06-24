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
use Rubix\ML\Loggers\Screen;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Pipeline;
use Rubix\ML\Transformers\MissingDataImputer;
use Rubix\ML\Transformers\NumericStringConverter;
use Rubix\ML\Transformers\OneHotEncoder;

class SimilarApartmentsTest extends \PHPUnit\Framework\TestCase
{

  public function testGetSimilar()
  {
    $data      = new CSV(__DIR__ . '/traininput/apartments_1k.csv', true);
    $data      = iterator_to_array($data->getIterator());
    $data = array_slice($data, 0, 10);

    $nr_groups = ceil(sqrt(count($data) / 2) );

    RubixService::train(
        $data,
        data_index_w_label: null,
        estimator_algorithm: new KMeans($nr_groups),
        transformers: [
        new NumericStringConverter(),
        new MissingDataImputer(),
        new OneHotEncoder(),
    ]);

//    $needs_similar_data = ["1.4400", "80", "2", "Belen", "Belén La Palma", "1", "Gas", "24 Horas", "5", "1", "1", "1"];
//    $needs_similar_data = [["1.3000","35","1","El Poblado","El Poblado","0","No tiene","24 Horas","5","1","0","1"]];
    $needs_similar_data = [["1.3000","85","3","Laureles","Calasanz","1","Gas","24 Horas","4","1","0","1"]];

    $similars = RubixService::predict($needs_similar_data);

    var_dump($similars);

  }


}
