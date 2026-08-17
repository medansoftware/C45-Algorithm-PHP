# C4.5 Algorithm - PHP

[![Packagist Version](https://img.shields.io/packagist/v/medansoftware/c45-algorithm-php.svg)](https://packagist.org/packages/medansoftware/c45-algorithm-php)
[![Tests](https://github.com/medansoftware/C45-Algorithm-PHP/actions/workflows/tests.yml/badge.svg)](https://github.com/medansoftware/C45-Algorithm-PHP/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/packagist/php-v/medansoftware/c45-algorithm-php.svg)](composer.json)
[![License](https://img.shields.io/github/license/medansoftware/C45-Algorithm-PHP.svg)](LICENSE)

A PHP implementation of the **C4.5 decision tree algorithm**, with support for building a tree from Excel/CSV files or plain PHP arrays, classifying new data, and exporting the resulting tree as a string, JSON, array, or Graphviz DOT diagram.

> 📄 [Example spreadsheet](examples/example.xlsx)

## Table of Contents

- [C4.5 Algorithm - PHP](#c45-algorithm---php)
	- [Table of Contents](#table-of-contents)
	- [Requirements](#requirements)
	- [Installation](#installation)
	- [Quick Start](#quick-start)
		- [From an Excel File](#from-an-excel-file)
		- [From a PHP Array](#from-a-php-array)
		- [From a CSV File](#from-a-csv-file)
	- [Classifying New Data](#classifying-new-data)
	- [Output Formats](#output-formats)
		- [As String](#as-string)
		- [As JSON](#as-json)
		- [As Array](#as-array)
		- [As Graphviz DOT Diagram](#as-graphviz-dot-diagram)
	- [Evaluating Accuracy](#evaluating-accuracy)
	- [Running Tests](#running-tests)
	- [License](#license)

## Requirements

- PHP ^8.1
- [phpoffice/phpspreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) ^2.0 || ^3.0

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require medansoftware/c45-algorithm-php
```

## Quick Start

### From an Excel File

```php
$c45 = new Algorithm\C45('examples/example.xlsx', 'PLAY');

$tree = $c45->initialize()->buildTree();

echo $tree->toString();
```

Or, using the fluent setup:

```php
$c45 = new Algorithm\C45();
$c45->loadFile('examples/example.xlsx');
$c45->setTargetAttribute('PLAY');

$tree = $c45->initialize()->buildTree();

echo $tree->toString();
```

### From a PHP Array

```php
$data = [
    ['OUTLOOK' => 'Sunny',  'TEMPERATURE' => 'Hot',  'HUMIDITY' => 'High',   'WINDY' => 'False', 'PLAY' => 'No'],
    ['OUTLOOK' => 'Sunny',  'TEMPERATURE' => 'Hot',  'HUMIDITY' => 'High',   'WINDY' => 'True',  'PLAY' => 'No'],
    ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Hot',  'HUMIDITY' => 'High',   'WINDY' => 'False', 'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Rainy',  'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High',   'WINDY' => 'False', 'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Rainy',  'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Rainy',  'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'True',  'PLAY' => 'No'],
    ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'True',  'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Sunny',  'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High',   'WINDY' => 'False', 'PLAY' => 'No'],
    ['OUTLOOK' => 'Sunny',  'TEMPERATURE' => 'Cool', 'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Rainy',  'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Sunny',  'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'Normal', 'WINDY' => 'True',  'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High',   'WINDY' => 'True',  'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Cloudy', 'TEMPERATURE' => 'Hot',  'HUMIDITY' => 'Normal', 'WINDY' => 'False', 'PLAY' => 'Yes'],
    ['OUTLOOK' => 'Rainy',  'TEMPERATURE' => 'Mild', 'HUMIDITY' => 'High',   'WINDY' => 'True',  'PLAY' => 'No'],
];

$input = new Algorithm\C45\DataInput;
$input->setData($data);
$input->setAttributes(['OUTLOOK', 'TEMPERATURE', 'HUMIDITY', 'WINDY', 'PLAY']);

$c45 = new Algorithm\C45();
$c45->c45 = $input;
$c45->setTargetAttribute('PLAY');

$tree = $c45->initialize()->buildTree();

echo $tree->toString();
```

### From a CSV File

```php
$input = new Algorithm\C45\DataInput;
$input->loadCsv('example.csv'); // delimiter defaults to ','

$c45 = new Algorithm\C45();
$c45->c45 = $input;
$c45->setTargetAttribute('PLAY');

$tree = $c45->initialize()->buildTree();

echo $tree->toString();
```

## Classifying New Data

```php
$newData = [
    'OUTLOOK'     => 'Sunny',
    'TEMPERATURE' => 'Hot',
    'HUMIDITY'    => 'High',
    'WINDY'       => 'False',
];

echo $tree->classify($newData); // "No"
```

## Output Formats

### As String

```php
echo $tree->toString();
```

### As JSON

```php
echo $tree->toJson();
```

### As Array

```php
print_r($tree->toArray());
```

### As Graphviz DOT Diagram

Useful for visualizing the tree with tools like [Graphviz Online](https://dreampuf.github.io/GraphvizOnline/) or the `dot` CLI:

```php
file_put_contents('tree.dot', $tree->toDot());
```

```bash
dot -Tpng tree.dot -o tree.png
```

## Evaluating Accuracy

Measure how well a built tree performs against a labeled test set (e.g. a held-out split of your data):

```php
$result = $c45->evaluate($tree, $testData); // rows must include the target attribute

echo $result['accuracy'];          // e.g. 0.86
echo $result['correct'] . '/' . $result['total'];
print_r($result['misclassified']); // rows the tree got wrong
```

## Running Tests

```bash
composer install
composer test
```

## License

Released under the [MIT License](LICENSE).

---

[Reference](https://github.com/juliardi/C45)

<p align="center"><b>Made with ❤️ + ☕ ~ Agung Dirgantara</b></p>