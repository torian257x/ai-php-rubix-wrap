
# AI PHP Rubix Wrap

A wrapper for  [Rubix ML](https://github.com/RubixML/ML) to make it very approachable


Example:

```php
    $report = RubixService::train($data, 'column_with_label');
```

Where `column_with_label` is the key of the multi dimensional array `$data` that contains the value that you want to predict.

Let's make a simple example:

```php
$apartment_data = [
        ['space_m2' =>  10, 'price' => 100],
        ['space_m2' =>  20, 'price' => 200],
        ['space_m2' =>  30, 'price' => 300],
        ['space_m2' =>  40, 'price' => 400],
        //...
        ['space_m2' => 280, 'price' => 2800],
        ['space_m2' => 290, 'price' => 2900],
        ['space_m2' => 300, 'price' => 3000],
];

$report = RubixService::train($apartment_data, 'price');

```

This performs the training and testing. `train()` actually internally runs a 
1. shuffle of `$data`
2. train against 70% of `$data`
3. test against 30% of `$data`

You can change that behaviour by using the argument `train_part_size` e.g. if you want to train on 80%, and test on 20% you would do `RubixService::train(... train_part_size: 0.8)`.


The `$report` contains the error analysis.

A short excerpt would be:

```php
var_export($report);

/* 
  array (
    'mean absolute error' => 68.88888888888889,
    ...
    'r squared' => 0.9796739130434783,
    ...
  )
*/ 
```

Mean absolute error is basically the actual error you can expect in average. So _in average_ if trying to predict an apartment given the space, you'd be off, in average, by 68.88$

`r squared` on the other hand gives more of a feeling how good the algorithm is in %. A high r squared means it works well.

Now you can predict new apartment data like so:

```php
    $prediction = RubixService::predict(['space_m2' => 250]);
    //$prediciton 2440
```

[See full example of above code here](https://github.com/torian257x/ai-php-rubix-wrap/blob/master/tests/Unit/ReadmeExamplesTest.php)



`RubixService::train()` will use a default estimator (machine learning algorithm) depending on the data. If you want to choose a different estimator I recommend reading here

[rubix ml choosing an estimator](https://docs.rubixml.com/latest/choosing-an-estimator.html)

Per default it uses [K-d Neighbors](https://docs.rubixml.com/latest/classifiers/kd-neighbors.html) or [K-d Neighbors Regressor](https://docs.rubixml.com/latest/regressors/kd-neighbors-regressor.html)

`RubixService::train()` takes as well [transformers](https://docs.rubixml.com/latest/preprocessing.html) 
