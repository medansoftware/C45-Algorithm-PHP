<?php

namespace Algorithm\C45;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Algorithm\C45\DataInput\DataInputInterface;

class DataInput implements DataInputInterface
{
	protected $file;

	protected $data;

	protected $classes;

	protected $attributes;

	public function __construct($file = null)
	{
		if (!empty($file))
		{
			$this->file = $file;
			$this->parseFile();
			$this->populateClasses();
		}
	}

	/**
	 * Set file
	 * 
	 * @param string $path_to_file
	 */
	public function setFile($path_to_file)
	{
		$this->file = $path_to_file;
		return $this;
	}

	/**
	 * Read file
	 * 
	 * @param  mixed  $spreadsheet 	instance of \PhpOffice\PhpSpreadsheet\Spreadsheet or null
	 * @param  integer $sheet 		set current sheet
	 * @return array
	 */
	public function readFile($spreadsheet = null, $sheet = 0)
	{
		if (!empty($this->file) OR !empty($spreadsheet))
		{
			if (empty($spreadsheet))
			{
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file);
			}

			return $spreadsheet->setActiveSheetIndex($sheet)->toArray();
		}
		else
		{
			throw new \Exception('File not set');
		}
	}

	/**
	 * Parse file
	 * 
	 * @param  mixed  $Spreadsheet 	instance of \PhpOffice\PhpSpreadsheet\Spreadsheet or null
	 * @param  integer $sheet      	set current sheet
	 * @return array
	 */
	public function parseFile($spreadsheet = null, $sheet = 0)
	{
		if (!empty($this->file) OR !empty($spreadsheet))
		{
			if (empty($spreadsheet))
			{
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file);
			}

			$data = $spreadsheet->setActiveSheetIndex($sheet)->toArray();

			$result = array();

			if ($data)
			{
				if (empty($this->attributes)) 
				{
					$this->attributes = $data[0];
					array_shift($data);
				}

				foreach ($data as $value)
				{
					$temp = array();

					for ($i = 0; $i < count($this->attributes); $i++)
					{
						$value[$i] = (is_bool($value[$i]))?($value[$i])?'True':'False':$value[$i];
						$attribute_name = $this->attributes[$i];

						$temp[$attribute_name] = trim($value[$i]);
					}

					$result[] = $temp;
				}
			}

			$this->data = $result;

			return $result;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAttributes($attributes = array())
	{
		$this->attributes = $attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasAttribute($attribute)
	{
		return array_search($attribute, $this->attributes) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setData($data = array())
	{
		$this->data = $data;
		$this->populateClasses();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getData($start = 0, $length = null)
	{
		if ($length == null)
		{
			return $this->data;
		}
		else
		{
			return array_slice($this->data, $start, $length);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getClasses($attributes = array())
	{
		if (!empty($attributes))
		{
			$result = [];

			foreach ($attributes as $value)
			{
				if ($this->hasAttribute($value))
				{
					$result[$value] = $this->classes[$value];
				}
			}

			return $result;
		}
		else
		{
			return $this->classes;
		}
	}

	/**
	 * Populate classes
	 */
	protected function populateClasses()
	{
		if (is_array($this->data))
		{
			$this->classes = [];

			for ($i = 0; $i < count($this->data); ++$i)
			{
				$data = $this->data[$i];

				foreach ($data as $key => $value)
				{
					if (array_key_exists($key, $this->classes))
					{
						if (array_search($value, $this->classes[$key]) === false)
						{
							array_push($this->classes[$key], $value);
						}
					}
					else
					{
						$this->classes[$key] = [$value];
					}
				}
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getByCriteria($criteria = array(), $length = null)
	{
		$result = [];

		foreach ($this->data as $row)
		{
			if ($length === 0)
			{
				break;
			}
			if ($this->isMatch($row, $criteria))
			{
				array_push($result, $row);
				--$length;
			}
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function countByCriteria($criteria = array())
	{
		$result = 0;

		foreach ($this->data as $row) 
		{
			if ($this->isMatch($row, $criteria))
			{
				++$result;
			}
		}

		return $result;
	}

	/**
	 * Checks whether $data matched $criteria.
	 * 
	 * @param  array  $data
	 * @param  array  $criteria
	 * @return boolean
	 */
	private function isMatch($data, $criteria)
	{
		$result = true;

		foreach ($criteria as $key => $value)
		{
			if ($this->hasAttribute($key))
			{
				if (is_array($data))
				{
					if ($data[$key] != $value)
					{
						$result = $result && false;
					}
				}
				elseif(is_object($data))
				{
					if ($data->{$key} != $value)
					{
						$result = $result && false;
					}
				}
				else
				{
					$result = false;
				}
			}
		}

		return $result;
	}
}