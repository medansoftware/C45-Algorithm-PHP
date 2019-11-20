<?php

namespace Algorithm\C45\Calculator;

use Algorithm\C45\DataInput\DataInputInterface;

abstract class AbstractCalculator
{
	protected $data;
	protected $targetAttribute;
	protected $targetValues;
	protected $targetCount;

	public function __construct(DataInputInterface $data, $targetAttribute)
	{
		$this->data = $data;
		$this->setTargetAttribute($targetAttribute);
	}

	public function setTargetAttribute($targetAttributeName)
	{
		$this->targetAttribute = $targetAttributeName;
		$this->targetValues = $this->getAttributeValues($this->targetAttribute);

		foreach ($this->targetValues as $value) {
			$criteria[$this->targetAttribute] = $value;
			$this->targetCount[$value] = $this->data->countByCriteria($criteria);
		}
	}

	protected function getAttributeValues($attributeName)
	{
		return $this->data->getClasses([$attributeName])[$attributeName];
	}

	protected function getAttributeNames($criteria)
	{
		$attributeNames = $this->data->getAttributes();

		foreach ($criteria as $key => $value) {
			$idx = array_search($key, $attributeNames);
			if ($idx !== false) {
				unset($attributeNames[$idx]);
			}
		}

		return $attributeNames;
	}
}