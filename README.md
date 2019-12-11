AssociativeArray
===

[![Build Status](https://travis-ci.org/nick-lai/associative-array.svg?branch=master)](https://travis-ci.org/nick-lai/associative-array)
[![codecov](https://codecov.io/gh/nick-lai/associative-array/branch/master/graph/badge.svg)](https://codecov.io/gh/nick-lai/associative-array)
[![Maintainability](https://api.codeclimate.com/v1/badges/619cef82d3eba2ea735c/maintainability)](https://codeclimate.com/github/nick-lai/associative-array/maintainability)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nick-lai/associative-array/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nick-lai/associative-array/?branch=master)
[![PHP 7 ready](https://php7ready.timesplinter.ch/nick-lai/associative-array/master/badge.svg)](https://travis-ci.org/nick-lai/associative-array)
[![Packagist](https://img.shields.io/packagist/v/nick-lai/associative-array.svg)](https://packagist.org/packages/nick-lai/associative-array)
[![Total Downloads](https://img.shields.io/packagist/dt/nick-lai/associative-array.svg?color=brightgreen)](https://packagist.org/packages/nick-lai/associative-array)

**A lightweight associative array library for PHP.**

## Table of Contents

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
    - [make()](#make)
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

$associativeArray = new AssociativeArray($data);

var_export([
    'first' => $associativeArray->first(),
    'last' => $associativeArray->last(),
    'count' => $associativeArray->count(),
    'sum(price)' => $associativeArray->sum('price'),
    'avg(price)' => $associativeArray->avg('price'),
]);
```

Result:

```
array (
  'first' =>
  array (
    'id' => 1001,
    'category' => 'C',
    'price' => 20,
  ),
  'last' =>
  array (
    'id' => 1005,
    'category' => 'B',
    'price' => 10,
  ),
  'count' => 5,
  'sum(price)' => 85,
  'avg(price)' => 17,
)
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
    1001 => ['id' => 1001, 'category' => 'C', 'price' => 30],
    1002 => ['id' => 1002, 'category' => 'A', 'price' => 25],
    1003 => ['id' => 1003, 'category' => 'B', 'price' => 10],
    1004 => ['id' => 1004, 'category' => 'X', 'price' => 60],
]);

$categories = [
    'A' => ['category' => 'A', 'desc' => 'A desc'],
    'B' => ['category' => 'B', 'desc' => 'B desc'],
    'C' => ['category' => 'C', 'desc' => 'C desc'],
    'D' => ['category' => 'D', 'desc' => 'D desc'],
];

$result = $associativeArray->innerJoin($categories, function ($leftRow, $rightRow) {
    return $leftRow['category'] === $rightRow['category'];
})->toArray();

var_export($result);
```

Result:

```
array (
  1001 =>
  array (
    'id' => 1001,
    'category' => 'C',
    'price' => 30,
    'desc' => 'C desc',
  ),
  1002 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
    'desc' => 'A desc',
  ),
  1003 =>
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
    1001 => ['id' => 1001, 'category' => 'C', 'price' => 30],
    1002 => ['id' => 1002, 'category' => 'A', 'price' => 25],
    1003 => ['id' => 1003, 'category' => 'B', 'price' => 10],
    1004 => ['id' => 1004, 'category' => 'X', 'price' => 60],
]);

$categories = [
    'A' => ['category' => 'A', 'desc' => 'A desc'],
    'B' => ['category' => 'B', 'desc' => 'B desc'],
    'C' => ['category' => 'C', 'desc' => 'C desc'],
    'D' => ['category' => 'D', 'desc' => 'D desc'],
];

$result = $associativeArray->leftJoin($categories, function ($leftRow, $rightRow) {
    return $leftRow['category'] === $rightRow['category'];
})->toArray();

var_export($result);
```

Result:

```
array (
  1001 =>
  array (
    'id' => 1001,      
    'category' => 'C', 
    'price' => 30,     
    'desc' => 'C desc',
  ),
  1002 =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
    'desc' => 'A desc',
  ),
  1003 =>
  array (
    'id' => 1003,
    'category' => 'B',
    'price' => 10,
    'desc' => 'B desc',
  ),
  1004 =>
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
    1001 => ['id' => 1001, 'category' => 'C', 'price' => 30],
    1002 => ['id' => 1002, 'category' => 'A', 'price' => 25],
    1003 => ['id' => 1003, 'category' => 'B', 'price' => 10],
    1004 => ['id' => 1004, 'category' => 'X', 'price' => 60],
]);

$categories = [
    'A' => ['category' => 'A', 'desc' => 'A desc'],
    'B' => ['category' => 'B', 'desc' => 'B desc'],
    'C' => ['category' => 'C', 'desc' => 'C desc'],
    'D' => ['category' => 'D', 'desc' => 'D desc'],
];

$result = $associativeArray->rightJoin($categories, function ($leftRow, $rightRow) {
    return $leftRow['category'] === $rightRow['category'];
})->toArray();

var_export($result);
```

Result:

```
array (
  'A' =>
  array (
    'category' => 'A',
    'desc' => 'A desc',
    'id' => 1002,
    'price' => 25,
  ),
  'B' =>
  array (
    'category' => 'B',
    'desc' => 'B desc',
    'id' => 1003,
    'price' => 10,
  ),
  'C' =>
  array (
    'category' => 'C',
    'desc' => 'C desc',
    'id' => 1001,
    'price' => 30,
  ),
  'D' =>
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

Keep index

```php
$associativeArray = new AssociativeArray([
    'X' => ['id' => 1001, 'category' => 'C', 'price' => 10],
    'Y' => ['id' => 1002, 'category' => 'A', 'price' => 25],
    'Z' => ['id' => 1003, 'category' => 'B', 'price' => 10],
]);

$result = $associativeArray->orderBy('category', 'asc', true)->toArray();

var_export($result);
```

Result:

```php
array (
  'Y' =>
  array (
    'id' => 1002,
    'category' => 'A',
    'price' => 25,
  ),
  'Z' =>
  array (
    'id' => 1003,
    'category' => 'B',
    'price' => 10,
  ),
  'X' =>
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

$result = $associativeArray->groupBy(['category', 'price']);

var_export($result);
```

Result:

```
array (
  'B' => 
  array (
    30 => 
    array (
      0 => 
      array (
        'id' => 1001,
        'category' => 'B',
        'price' => 30,
      ),
      1 => 
      array (
        'id' => 1003,
        'category' => 'B',
        'price' => 30,
      ),
    ),
  ),
  'A' => 
  array (
    25 => 
    array (
      0 => 
      array (
        'id' => 1002,
        'category' => 'A',
        'price' => 25,
      ),
    ),
    30 => 
    array (
      0 => 
      array (
        'id' => 1004,
        'category' => 'A',
        'price' => 30,
      ),
    ),
  ),
)
```

### make()

Create a new associative array instance.

```php
$data = [
    ['id' => 1001, 'category' => 'C', 'price' => 10],
    ['id' => 1002, 'category' => 'A', 'price' => 25],
    ['id' => 1003, 'category' => 'B', 'price' => 10],
];

var_export(AssociativeArray::make($data)->first());
```

Result:

```
array (
  'id' => 1001,
  'category' => 'C',
  'price' => 10,
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
