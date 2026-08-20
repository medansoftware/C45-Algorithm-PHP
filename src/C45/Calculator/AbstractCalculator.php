<?php

declare(strict_types=1);

namespace Algorithm\C45\Calculator;

use Algorithm\C45\DataInput\DataInputInterface;

abstract class AbstractCalculator
{
	protected $data;
	protected $targetAttribute;
	protected $targetValues;
	protected $targetCount;

	public function __construct(DataInputInterface $data, string $targetAttribute)
	{
		$this->data = $data;
		$this->setTargetAttribute($targetAttribute);
	}

	public function setTargetAttribute(string $targetAttributeName): void
	{
		$this->targetAttribute = $targetAttributeName;
		$this->targetValues = $this->getAttributeValues($this->targetAttribute);

		foreach ($this->targetValues as $value) {
			$criteria[$this->targetAttribute] = $value;
			$this->targetCount[$value] = $this->data->countByCriteria($criteria);
		}
	}

	protected function getAttributeValues(string $attributeName): array
	{
		return $this->data->getClasses([$attributeName])[$attributeName];
	}

	protected function getAttributeNames(array $criteria): array
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