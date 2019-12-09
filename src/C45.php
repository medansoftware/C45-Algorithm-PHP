<?php

namespace Algorithm;

use Algorithm\C45\TreeNode;
use Algorithm\C45\DataInput;
use Algorithm\C45\Calculator\GainCalculator;
use Algorithm\C45\Calculator\GainRatioCalculator;
use Algorithm\C45\Calculator\SplitInfoCalculator;

class C45
{
	/**
	 * split gain
	 * 
	 * @var integer
	 */
	public $split_gain;

	/**
	 * @var Algorithm\C45\DataInput
	 */
	public $c45;

	/**
	 * target attribute's values
	 * 
	 * @var array
	 */
	public $target_values;

	/**
	 * target attribute
	 * 
	 * @var string
	 */
	public $target_attribute;

	/**
	 * split criterion
	 * 
	 * @var integer
	 */
	public $split_criterion;

	/**
	 * total target's count split by target value
	 * 
	 * @var array
	 */
	public $targetCount;	

	/**
	 * @var Algorithm\C45\Calculator\GainCalculator
	 */
	public $gainCalculator;

	/**
	 * @var Algorithm\C45\Calculator\SplitInfoCalculator
	 */
	public $splitInfoCalculator;

	/**
	 * @var Algorithm\C45\Calculator\GainRatioCalculator
	 */
	public $gainRatioCalculator;

	public function __construct($file = null, $target_attribute = null)
	{
		$this->c45 = new \Algorithm\C45\DataInput($file);
		$this->setTargetAttribute($target_attribute);
	}

	/**
	 * Load file
	 * 
	 * @param  string $file
	 * @return Algorithm\C45
	 */
	public function loadFile($file)
	{
		$this->c45 = new \Algorithm\C45\DataInput($file);
		return $this;
	}

	/**
	 * Set target attribute
	 * 
	 * @param string $target_attribute
	 * @return Algorithm\C45
	 */
	public function setTargetAttribute($target_attribute)
	{
		if (!empty($target_attribute))
		{
			$attributes = $this->c45->getAttributes();

			if (in_array($target_attribute, $attributes))
			{
				$this->target_attribute = $target_attribute;
			}
			else
			{
				$this->target_attribute = end($attributes);
			}
		}

		return $this;
	}

	/**
	 * Initialize class
	 * 
	 * @return object Algorithm\C45
	 */
	public function initialize()
	{
		$this->target_values = $this->getAttributeValues($this->target_attribute);

		foreach ($this->target_values as $value) 
		{
			$criteria[$this->target_attribute] = $value;
			$this->targetCount[$value] = $this->c45->countByCriteria($criteria);
		}

		$this->gainCalculator = new GainCalculator($this->c45, $this->target_attribute);
		$this->splitInfoCalculator = new SplitInfoCalculator($this->c45, $this->target_attribute);
		$this->gainRatioCalculator = new GainRatioCalculator($this->c45, $this->target_attribute);

		return $this;
	}

	/**
	 * Build decision tree
	 * 
	 * @param  array  $criteria
	 * @return TreeNode
	 */
	public function buildTree($criteria = array())
	{
		$tree_node = new TreeNode;
	
		$check_class = $this->isBelongToOneClass($criteria);

		if ($check_class['return'])
		{
			$tree_node->setAttribute($this->target_attribute);
			$tree_node->addChild('result', $check_class['class']);
			$tree_node->setIsLeaf(true);
			return $tree_node;
		}

		$split_criterion = $this->calculateSplitCriterion($criteria);
		$best_attribute_name = $this->getBiggestArrayAttribute($split_criterion);
		$best_attribute_values = $this->getAttributeValues($best_attribute_name);

		$tree_node->setAttribute($best_attribute_name);
		unset($split_criterion[$best_attribute_name]);

		foreach ($best_attribute_values as $value)
		{
			$criteria[$best_attribute_name] = $value;
			$targetCount = $this->countTargetByCriteria($criteria);
			$tree_node->addClassesCount($value, $targetCount);

			if (array_sum($targetCount) == 0) 
			{
				$target_count2 = $this->countTargetByCriteria([$best_attribute_name => $value]);
				$biggest_class = $this->getBiggestArrayAttribute($target_count2);

				$child = new TreeNode();
				$child->setParent($tree_node);
				$child->setAttribute($this->target_attribute);
				$child->addChild('result', $biggest_class);
				$child->setIsLeaf(true);

				$tree_node->addChild($value, $child);
			}
			elseif (!empty($split_criterion))
			{
				$child = $this->buildTree($criteria);
				$child->setParent($tree_node);
				$tree_node->addChild($value, $child);
			}
			else
			{
				$class_probability = $this->calculateClassProbability($criteria);
				$biggest_class = $this->getBiggestArrayAttribute($class_probability);

				$child = new TreeNode();
				$child->setParent($tree_node);
				$child->setAttribute($this->target_attribute);
				$child->addChild('result', $biggest_class);
				$child->setIsLeaf(true);

				$tree_node->addChild($value, $child);
			}
		}

		return $tree_node;
	}

	public function calculateSplitCriterion($criteria = array())
	{
		$gain = $this->gainCalculator->calculateGainAllAttributes($criteria);

		if ($this->split_criterion == $this->split_gain)
		{
			return $gain;
		}
		else
		{
			$split_info = $this->splitInfoCalculator->calculateSplitInfoAllAttributes($criteria);
			$gain_ratio = $this->gainRatioCalculator->calculateGainRatio($gain, $split_info);

			return $gain_ratio;
		}
	}

	public function calculateClassProbability($criteria = array())
	{
		$count_target = $this->countTargetByCriteria($criteria);
		$total = array_sum($count_target);
		$class_probability = [];

		foreach ($this->target_values as $value)
		{
			$class_probability[$value] = $this->classProbability($count_target[$value], $total);
		}

		return $class_probability;
	}

	public function classProbability($count_target_class, $total)
	{
		if ($total == 0)
		{
			return 0;
		}

		return $count_target_class / $total;
	}

	public function isBelongToOneClass($criteria = array())
	{
		$countAll = $this->c45->countByCriteria($criteria);

		foreach ($this->target_values as $value)
		{
			$criteria[$this->target_attribute] = $value;
			$count_by_target = $this->c45->countByCriteria($criteria);
			unset($criteria[$this->target_attribute]);
			if ($countAll === $count_by_target)
			{
				return [
					'return' => true,
					'class' => $value,
				];
			}
		}

		return ['return' => false];
	}

	public function getBiggestArrayAttribute($array = array())
	{
		array_multisort($array, SORT_DESC);
		reset($array);
		$key = key($array);

		return $key;
	}

	public function countTargetByCriteria($criteria = array())
	{
		$target_count = [];

		foreach ($this->target_values as $value)
		{
			$criteria[$this->target_attribute] = $value;
			$target_count[$value] = $this->c45->countByCriteria($criteria);
		}

		unset($criteria[$this->target_attribute]);

		return $target_count;
	}

	public function getAttributeValues($attribute_name)
	{
		return $this->c45->getClasses([$attribute_name])[$attribute_name];
	}
}