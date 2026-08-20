<?php

declare(strict_types=1);

namespace Algorithm\C45\Calculator;

class GainCalculator extends AbstractCalculator
{
	public function calculateGainAllAttributes(array $criteria = []): array
	{
		$attributeNames = $this->getAttributeNames($criteria);

		$gain = [];

		foreach ($attributeNames as $value) 
		{
			if ($value != $this->targetAttribute) 
			{
				$gain[$value] = $this->calculateGainOfAttribute($value, $criteria);
			}
		}

		return $gain;
	}

	public function calculateGainOfAttribute(string $attributeName, array $criteria = []): float
	{
		$gain = 0;
		$attributeCount = [];
		$attributeValues = $this->getAttributeValues($attributeName);

		foreach ($attributeValues as $value) 
		{
			$criteria[$attributeName] = $value;
			foreach ($this->targetValues as $targetValue) 
			{
				$criteria[$this->targetAttribute] = $targetValue;
				$attributeCount[$value][$targetValue] = $this->data->countByCriteria($criteria);
			}
		}

		$gain = $this->gain($this->targetCount, $attributeCount);

		return $gain;
	}

	private function gain(array $classifier_values, array $values): float
	{
		$entropy_all = $this->entropy($classifier_values);
		$total_records = 0;

		foreach ($values as $sub_values) 
		{
			$total_records += array_sum($sub_values);
		}

		$gain = 0;

		if ($total_records > 0)
		{
			foreach ($values as $sub_values)
			{
				$sub_total_values = array_sum($sub_values);
				$entropy = $this->entropy($sub_values);
				$gain += ($sub_total_values / $total_records) * $entropy;
			}
		}

		$gain = $entropy_all - $gain;

		return $gain;
	}

	private function entropy(array $values): float
	{
		$result = 0;
		$sum = array_sum($values);

		foreach ($values as $value) 
		{
			if ($value > 0) 
			{
				$proportion = $value / $sum;
				$result += -($proportion * log($proportion, 2));
			}
		}

		return $result;
	}
}