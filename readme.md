# AssociativeArray

[![Build Status](https://travis-ci.org/nick-lai/associative-array.svg?branch=master)](https://travis-ci.org/nick-lai/associative-array)
[![codecov](https://codecov.io/gh/nick-lai/associative-array/branch/master/graph/badge.svg)](https://codecov.io/gh/nick-lai/associative-array)
[![Maintainability](https://api.codeclimate.com/v1/badges/619cef82d3eba2ea735c/maintainability)](https://codeclimate.com/github/nick-lai/associative-array/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nick-lai/associative-array/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nick-lai/associative-array/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/nick-lai/associative-array/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

[![PHP 7 ready](https://php7ready.timesplinter.ch/nick-lai/associative-array/master/badge.svg)](https://travis-ci.org/nick-lai/associative-array)
[![Latest Stable Version](https://poser.pugx.org/nick-lai/associative-array/v/stable)](https://packagist.org/packages/nick-lai/associative-array)
[![Total Downloads](https://poser.pugx.org/nick-lai/associative-array/downloads)](https://packagist.org/packages/nick-lai/associative-array)
[![License](https://poser.pugx.org/nick-lai/associative-array/license)](https://packagist.org/packages/nick-lai/associative-array)

# Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Basic Usage](#basic-usage)
    - [select()](#select)
    - [where()](#where)
    - [innerJoin()](#innerjoin)
    - [leftJoin()](#leftjoin)
    - [rightJoin()](#rightjoin)
    - [orderBy()](#orderby)
    - [groupBy()](#groupby)
    - [first()](#first)
    - [last()](#last)
    - [count()](#count)
    - [sum()](#sum)
    - [avg()](#avg)
    - [toArray()](#toarray)
    - [Array Access](#array-access)
    - [Traversable](#traversable)
- [License](#license)

## Requirements

AssociativeArray requires PHP >= 7.0.0

## Installation

```sh
composer require nick-lai/associative-array
```

## Basic Usage

```php
use NickLai\AssociativeArray;

$data = [
    ['id' => 1001, 'category' => 'C', 'price' => 20],
    ['id' => 1002, 'category' => 'B', 'price' => 15],
    ['id' => 1003, 'category' => 'A', 'price' => 15],
    ['id' => 1004, 'category' => 'A', 'price' => 25],
    ['id' => 1005, 'category' => 'B', 'price' => 10],
];

$assciativeArray = new AssociativeArray($data);

$priceBetween0and20Rows = $assciativeArray->where(function ($row) {
    return 0 <= $row['price'] && $row['price'] <= 20;
})->orderBy(['category', 'price']);

$priceBetween15and25Rows = $assciativeArray->where(function ($row) {
    return 15 <= $row['price'] && $row['price'] <= 25;
})->orderBy(['category', 'price'], ['asc', 'desc']);

echo "Price between 0 and 20:" . PHP_EOL;
print_r($priceBetween0and20Rows->toArray());
echo "count(): " . $priceBetween0and20Rows->count() . PHP_EOL;
echo "sum('price'): " . $priceBetween0and20Rows->sum('price') . PHP_EOL . PHP_EOL;

echo "Price between 15 and 25:" . PHP_EOL;
print_r($priceBetween15and25Rows->toArray());
echo "count(): " . $priceBetween15and25Rows->count() . PHP_EOL;
echo "sum('price'): " . $priceBetween15and25Rows->sum('price');
```

Result:

```
Price between 0 and 20:
Array
(
    [0] => Array
        (
            [id] => 1003   
            [category] => A
            [price] => 15  
        )

    [1] => Array
        (
            [id] => 1005   
            [category] => B
            [price] => 10  
        )

    [2] => Array
        (
            [id] => 1002
            [category] => B
            [price] => 15
        )

    [3] => Array
        (
            [id] => 1001
            [category] => C
            [price] => 20
        )

)
count(): 4
sum('price'): 60

Price between 15 and 25:
Array
(
    [0] => Array
        (
            [id] => 1004
            [category] => A
            [price] => 25
        )

    [1] => Array
        (
            [id] => 1003
            [category] => A
            [price] => 15
        )

    [2] => Array
        (
            [id] => 1002
            [category] => B
            [price] => 15
        )

    [3] => Array
        (
            [id] => 1001
            [category] => C
            [price] => 20
        )

)
count(): 4
sum('price'): 75
```

### select()

Get rows of selected columns.

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 30],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray->select(['id', 'price'])->toArray());
var_export($associativeArray->select(['category', 'price'])->toArray());
```

Result:

```
array (
  0 =>
  array (
    'id' => 1001,
    'price' => 30,
  ),
  1 =>
  array (
    'id' => 1002,
    'price' => 25,
  ),
  2 =>
  array (
    'id' => 1003,
    'price' => 10,
  ),
)

array (
  0 =>
  array (
    'category' => 'C',
    'price' => 30,
  ),
  1 =>
  array (
    'category' => 'A',
    'price' => 25,
  ),
  2 =>
  array (
    'category' => 'B',
    'price' => 10,
  ),
)
```

### where()

Filter the rows using the given callback.

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 30],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

$result = $associativeArray->where(function ($row) {
    return $row['price'] > 10;
})->toArray();

var_export($result);
```

Result:

```
array (
  0 =>
  array (
    'id' => 1001,
    'category' => 'C',
    'price' => 30,
  ),
  1 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
  ),
)
```

### innerJoin()

Inner join rows

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 30],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
    ['id' => 1004, 'category' => 'X', 'price' => 60],
]);

$categories = [
    ['category' => 'A', 'desc' => 'A desc'],
    ['category' => 'B', 'desc' => 'B desc'],
    ['category' => 'C', 'desc' => 'C desc'],
    ['category' => 'D', 'desc' => 'D desc'],
];

$result = $associativeArray->innerJoin($categories, function ($leftRow, $rightRow) {
    return $leftRow['category'] === $rightRow['category'];
})->toArray();

var_export($result);
```

Result:

```
array (
  0 =>
  array (
    'id' => 1001,
    'category' => 'C',
    'price' => 30,
    'desc' => 'C desc',
  ),
  1 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
    'desc' => 'A desc',
  ),
  2 =>
  array (
    'id' => 1003,
    'category' => 'B',
    'price' => 10,
    'desc' => 'B desc',
  ),
)
```

### leftJoin()

Left join rows

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 30],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
    ['id' => 1004, 'category' => 'X', 'price' => 60],
]);

$categories = [
    ['category' => 'A', 'desc' => 'A desc'],
    ['category' => 'B', 'desc' => 'B desc'],
    ['category' => 'C', 'desc' => 'C desc'],
    ['category' => 'D', 'desc' => 'D desc'],
];

$result = $associativeArray->leftJoin($categories, function ($leftRow, $rightRow) {
    return $leftRow['category'] === $rightRow['category'];
})->toArray();

var_export($result);
```

Result:

```
array (
  0 =>
  array (
    'id' => 1001,      
    'category' => 'C', 
    'price' => 30,     
    'desc' => 'C desc',
  ),
  1 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
    'desc' => 'A desc',
  ),
  2 =>
  array (
    'id' => 1003,
    'category' => 'B',
    'price' => 10,
    'desc' => 'B desc',
  ),
  3 =>
  array (
    'id' => 1004,
    'category' => 'X',
    'price' => 60,
    'desc' => NULL,
  ),
)
```

### rightJoin()

Right join rows

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 30],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
    ['id' => 1004, 'category' => 'X', 'price' => 60],
]);

$categories = [
    ['category' => 'A', 'desc' => 'A desc'],
    ['category' => 'B', 'desc' => 'B desc'],
    ['category' => 'C', 'desc' => 'C desc'],
    ['category' => 'D', 'desc' => 'D desc'],
];

$result = $associativeArray->rightJoin($categories, function ($leftRow, $rightRow) {
    return $leftRow['category'] === $rightRow['category'];
})->toArray();

var_export($result);
```

Result:

```
array (
  0 =>
  array (
    'category' => 'A',
    'desc' => 'A desc',
    'id' => 1002,
    'price' => 25,
  ),
  1 =>
  array (
    'category' => 'B',
    'desc' => 'B desc',
    'id' => 1003,
    'price' => 10,
  ),
  2 =>
  array (
    'category' => 'C',
    'desc' => 'C desc',
    'id' => 1001,
    'price' => 30,
  ),
  3 =>
  array (
    'category' => 'D',
    'desc' => 'D desc',
    'id' => NULL,
    'price' => NULL,
  ),
)
```

### orderBy()

Order by keys

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

$result = $associativeArray->orderBy(['price', 'category'], ['desc', 'asc'])->toArray();

var_export($result);
```

Result:

```
array (
  0 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
  ),
  1 =>
  array (
    'id' => 1003,
    'category' => 'B',
    'price' => 10,
  ),
  2 =>
  array (
    'id' => 1001,
    'category' => 'C',
    'price' => 10,
  ),
)
```

### groupBy()

Groups an associative array by keys.

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'B', 'price' => 30],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 30],
    ['id' => 1004, 'category' => 'A', 'price' => 30],
]);

$result = $associativeArray->groupBy(['category', 'price'])->toArray();

var_export($result);
```

Result:

```
array (
  0 =>
  array (
    'id' => 1001,
    'category' => 'B',
    'price' => 30,
  ),
  1 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
  ),
  2 =>
  array (
    'id' => 1004,
    'category' => 'A',
    'price' => 30,
  ),
)
```

### first()

Return the first row

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray->first());
```

Result:

```
array (
  'id' => 1001,
  'category' => 'C',
  'price' => 10,
)
```

### last()

Return the last row

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray->last());
```

Result:

```
array (
  'id' => 1003,
  'category' => 'B',
  'price' => 10,
)
```

### count()

Count the number of rows in the associative array.

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray->count());
```

Result:

```
3
```

### sum()

Get the sum of a given key.

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray->sum('price'));
```

Result:

```
45
```

### avg()

Get the average value of a given key.

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray->avg('price'));
```

Result:

```
15
```

### toArray()

Get the instance as an array.

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray->toArray());
```

Result:

```
array (
  0 =>
  array (
    'id' => 1001,
    'category' => 'C',
    'price' => 10,
  ),
  1 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
  ),
  2 =>
  array (
    'id' => 1003,
    'category' => 'B',
    'price' => 10,
  ),
)
```

### Array Access

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

var_export($associativeArray[0]);
```

Result:

```
array (
  'id' => 1001,
  'category' => 'C',
  'price' => 10,
)
```

### Traversable

```php
$associativeArray = new AssociativeArray([
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

foreach ($associativeArray as $row) {
    echo $row['category'] . PHP_EOL;
}
```

Result:

```
C
A
B

```

## License

AssociativeArray is released under the MIT Licence. See the bundled LICENSE file for details.
