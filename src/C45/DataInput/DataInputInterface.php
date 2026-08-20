<?php

declare(strict_types=1);

namespace Algorithm\C45\DataInput;

interface DataInputInterface
{

	public function setFile(string $path_to_file);

	/**
	 * set attributes
	 * 
	 * @param array $attributes
	 */
	public function setAttributes(array $attributes = array());

	/**
	 * Check attribute name
	 * 
	 * @param  string $attribute
	 * @return boolean
	 */
	public function hasAttribute(string $attribute): bool;

	/**
	 * Get attributes name
	 * 
	 * @return array
	 */
	public function getAttributes(): ?array;

	/**
	 * Set data
	 * 
	 * @param array $data
	 */
	public function setData(array $data = array());

	/**
	 * Get data
	 * 
	 * @param  integer $start
	 * @param  integer|null $length
	 * @return array
	 */
	public function getData(int $start = 0, ?int $length = null): array;

	/**
	 * Classes list
	 * 
	 * @param  array  $attributes list of attribute(s)
	 * @return array
	 */
	public function getClasses(array $attributes = array()): array;


	/**
	 * Get rows that matched the $criteria
	 * 
	 * @param  array  $criteria [{attribute} => {value}]
	 * @param  integer|null $length   ammount of data
	 * @return array
	 */
	public function getByCriteria(array $criteria = array(), ?int $length = null): array;

	/**
	 * Counts rows that matched the criteria.
	 * 
	 * @param  array  $criteria
	 * @return int
	 */
	public function countByCriteria(array $criteria = array()): int;
}