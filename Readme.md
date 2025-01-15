# C45 Algorithm - PHP Language

> [**Use Example File**](example.xlsx)

## Installation

The recommended way to install the C45 PHP library is through [Composer](https://getcomposer.org) :

```bash
composer require medansoftware/c45-algorithm-php
```

## Manual Installation

```bash
composer dump-autoload
```

## Setup

```php
$c45 = new Algorithm\C45('example.xlsx', 'PLAY');
$initialize = $c45->initialize(); // initialize
$buildTree = $initialize->buildTree(); // build tree

$arrayTree = $buildTree->toArray(); // set to array
$stringTree = $buildTree->toString(); // set to string

echo "<pre>";
print_r ($arrayTree);
echo "</pre>";

echo $stringTree;
```
or 

```php
$c45 = new Algorithm\C45();
$c45->loadFile('example.xlsx'); // load example file
$c45->setTargetAttribute('PLAY'); // set target attribute

$initialize = $c45->initialize(); // initialize
$buildTree = $initialize->buildTree(); // build tree

$arrayTree = $buildTree->toArray(); // set to array
$stringTree = $buildTree->toString(); // set to string

echo "<pre>";
print_r ($arrayTree);
echo "</pre>";

echo $stringTree;
```

## Other Examples

```php
$c45 = new Algorithm\C45();
$c45->loadFile('example.xlsx')->setTargetAttribute('PLAY')->initialize();

echo "<pre>";
print_r ($c45->buildTree()->toString()); // print as string
echo "</pre>";

echo "<pre>";
print_r ($c45->buildTree()->toJson()); // print as JSON
echo "</pre>";

echo "<pre>";
print_r ($c45->buildTree()->toArray()); // print as array
echo "</pre>";
```

## Initialize Data from Array

```php
$c45 = new Algorithm\C45();
$input = new Algorithm\C45\DataInput;
$data = array(
	array(
		"OUTLOOK" => "Sunny",
		"TEMPERATURE" => "Hot",
		"HUMIDITY" => "High",
		"WINDY" => "False",
		"PLAY" => "No"
	),
	array(
		"OUTLOOK" => "Sunny",
		"TEMPERATURE" => "Hot",
		"HUMIDITY" => "High",
		"WINDY" => "True",
		"PLAY" => "No"
	),
	array(
		"OUTLOOK" => "Cloudy",
		"TEMPERATURE" => "Hot",
		"HUMIDITY" => "High",
		"WINDY" => "False",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Rainy",
		"TEMPERATURE" => "Mild",
		"HUMIDITY" => "High",
		"WINDY" => "False",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Rainy",
		"TEMPERATURE" => "Cool",
		"HUMIDITY" => "Normal",
		"WINDY" => "False",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Rainy",
		"TEMPERATURE" => "Cool",
		"HUMIDITY" => "Normal",
		"WINDY" => "True",
		"PLAY" => "No"
	),
	array(
		"OUTLOOK" => "Cloudy",
		"TEMPERATURE" => "Cool",
		"HUMIDITY" => "Normal",
		"WINDY" => "True",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Sunny",
		"TEMPERATURE" => "Mild",
		"HUMIDITY" => "High",
		"WINDY" => "False",
		"PLAY" => "No"
	),
	array(
		"OUTLOOK" => "Sunny",
		"TEMPERATURE" => "Cool",
		"HUMIDITY" => "Normal",
		"WINDY" => "False",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Rainy",
		"TEMPERATURE" => "Mild",
		"HUMIDITY" => "Normal",
		"WINDY" => "False",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Sunny",
		"TEMPERATURE" => "Mild",
		"HUMIDITY" => "Normal",
		"WINDY" => "True",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Cloudy",
		"TEMPERATURE" => "Mild",
		"HUMIDITY" => "High",
		"WINDY" => "True",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Cloudy",
		"TEMPERATURE" => "Hot",
		"HUMIDITY" => "Normal",
		"WINDY" => "False",
		"PLAY" => "Yes"
	),
	array(
		"OUTLOOK" => "Rainy",
		"TEMPERATURE" => "Mild",
		"HUMIDITY" => "High",
		"WINDY" => "True",
		"PLAY" => "No"
	)
);

// Initialize Data
$input->setData($data); // Set data from array
$input->setAttributes(array('OUTLOOK', 'TEMPERATURE', 'HUMIDITY', 'WINDY', 'PLAY')); // Set attributes of data

// Initialize C4.5
$c45->c45 = $input; // Set input data
$c45->setTargetAttribute('PLAY'); // Set target attribute
$initialize = $c45->initialize(); // initialize

// Build Output
$buildTree = $initialize->buildTree(); // Build tree
$arrayTree = $buildTree->toArray(); // Set to array
$stringTree = $buildTree->toString(); // Set to string

echo "<pre>";
print_r ($arrayTree);
echo "</pre>";

echo $stringTree;
```

```php

$new_data = array(
	'OUTLOOK' => 'Sunny',
	'TEMPERATURE' => 'Hot',
	'HUMIDITY' => 'High',
	'WINDY' => FALSE
);

echo $c45->initialize()->buildTree()->classify($new_data); // print "No"
```

[Refrence](https://github.com/juliardi/C45)

<p align="center"><b>Made with ❤️ + ☕ ~ Agung Dirgantara</b></p>
