<?php

namespace Algorithm\C45\Calculator;

class GainCalculator extends AbstractCalculator
{
	public function calculateGainAllAttributes($criteria = [])
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

	public function calculateGainOfAttribute($attributeName, $criteria = [])
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

	private function gain($classifier_values, $values)
	{
		$entropy_all = $this->entropy($classifier_values);
		$total_records = 0;

		foreach ($values as $sub_values) 
		{
			$total_records += array_sum($sub_values);
		}

		$gain = 0;

		foreach ($values as $sub_values) 
		{
			try 
			{
				$sub_total_values = array_sum($sub_values);
				$entropy = $this->entropy($sub_values);
				$gain += ($sub_total_values / $total_records) * $entropy;
			} 
			catch (\Exception $e) 
			{
				error_log($e->getMessage());
				error_log($e->getTraceAsString());
			}
		}

		$gain = $entropy_all - $gain;

		return $gain;
	}

	private function entropy(array $values)
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