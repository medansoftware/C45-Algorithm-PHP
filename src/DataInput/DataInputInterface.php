<?php

namespace Algorithm\C45\DataInput;

interface DataInputInterface
{

	public function setFile($path_to_file);

	/**
	 * set attributes
	 * 
	 * @param array $attributes
	 */
	public function setAttributes($attributes = array());

	/**
	 * Check attribute name
	 * 
	 * @param  string $attribute
	 * @return boolean
	 */
	public function hasAttribute($attribute);

	/**
	 * Get attributes name
	 * 
	 * @return array
	 */
	public function getAttributes();

	/**
	 * Set data
	 * 
	 * @param array $data
	 */
	public function setData($data = array());

	/**
	 * Get data
	 * 
	 * @param  integer $start
	 * @param  integer $length
	 * @return array
	 */
	public function getData($start = 0, $length = null);

	/**
	 * Classes list
	 * 
	 * @param  array  $attributes list of attribute(s)
	 * @return array
	 */
	public function getClasses($attributes = array());


	/**
	 * Get rows that matched the $criteria
	 * 
	 * @param  array  $criteria [{attribute} => {value}]
	 * @param  integer $length   ammount of data
	 * @return array
	 */
	public function getByCriteria($criteria = array(), $length = null);

	/**
	 * Counts rows that matched the criteria.
	 * 
	 * @param  array  $criteria [description]
	 * @return [type]           [description]
	 */
	public function countByCriteria($criteria = array());
}