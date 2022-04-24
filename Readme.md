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

---

<p align="center"><img src="https://cdn-images-1.medium.com/max/738/1*G95uyokAH4JC5Ppvx4LmoQ@2x.png" width="280"></p>

[**PATREON**](https://www.patreon.com/agoenks29D)

[**PAYPAL**](https://www.paypal.me/agungdirgantara)

**[ETH](https://www.blockchain.com/eth/address/0x251b36840557cCe9A245f07E1b834bCfb7354FDb) : 0x251b36840557cCe9A245f07E1b834bCfb7354FDb**

**[DOGE](https://dogechain.info/address/DFmES6KZLQXimXduXwKmooykMsjhWmT1tU) : DFmES6KZLQXimXduXwKmooykMsjhWmT1tU**

**[BITCOIN](https://www.blockchain.com/btc/address/1MEqUeg7fXTkBMFWfJZE9yJREsKZ4SUxQM) : 1MEqUeg7fXTkBMFWfJZE9yJREsKZ4SUxQM**

**[BITCOIN CASH](https://www.blockchain.com/bch/address/qzrllcyrjwvpnuur5kpeyp03p246fzsgzvhleswr6f) : qzrllcyrjwvpnuur5kpeyp03p246fzsgzvhleswr6f**

### Social Media : 

<a class="social_link" href="https://fb.me/agoenks29D">
	<img src="https://static.xx.fbcdn.net/rsrc.php/yo/r/iRmz9lCMBD2.ico" width="32" style="margin-bottom: 2px;">
</a>

<a class="social_link" href="https://instragram.com/agoenks29D">
	<img src="https://www.instagram.com/static/images/ico/favicon.ico/36b3ee2d91ed.ico" width="32">
</a>

<a class="social_link" href="https://t.me/agoenks29D">
	<img src="https://web.telegram.org/favicon.ico" width="32">
</a>

<a class="social_link" href="https://api.whatsapp.com/send?phone=6282167368585&text=Hello,i get your contact from github">
	<img src="https://static.whatsapp.net/rsrc.php/v3/yP/r/rYZqPCBaG70.png" width="36">
</a>

<a class="social_link" href="https://www.youtube.com/channel/UCwXyVSMRqAuyyQtXVoMrf2A?view_as=subscriber&sub_cotnfirmation=1">
	<img src="https://s.ytimg.com/yts/img/favicon_48-vflVjB_Qk.png" width="38">
</a> 

<p></p>

<p align="center"><b>Made with ❤️ + ☕ ~ Agung Dirgantara</b></p>