<?php

declare(strict_types=1);

namespace Algorithm\C45;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Algorithm\C45\DataInput\DataInputInterface;

class DataInput implements DataInputInterface
{
	protected $file;

	protected $data;

	protected $classes;

	protected $attributes;

	/**
	 * Index of attribute => value => [row index => true], built once per
	 * data load (populateClasses()) so that countByCriteria() and
	 * getByCriteria() can resolve matches via set intersection instead of
	 * scanning the entire dataset on every call. This matters a lot in
	 * practice: buildTree() calls these methods recursively for every
	 * attribute value at every node, so an O(n) scan per call becomes an
	 * O(n * tree size) cost overall on the unindexed version.
	 *
	 * Rows with a null or empty-string value for an attribute are treated
	 * as having a missing value for that attribute: they are left out of
	 * the index (and out of getClasses()) for that attribute, so they are
	 * simply excluded from criteria matches on it rather than being
	 * counted as their own "empty" class.
	 *
	 * @var array<string, array<string, array<int, true>>>
	 */
	protected $index = [];

	public function __construct(?string $file = null)
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
	public function setFile(string $path_to_file): self
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
	public function readFile($spreadsheet = null, int $sheet = 0): array
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
	public function parseFile($spreadsheet = null, int $sheet = 0): array
	{
		if (empty($this->file) && empty($spreadsheet))
		{
			return [];
		}

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
					$cell = $value[$i] ?? null;

					if (is_bool($cell))
					{
						$cell = $cell ? 'True' : 'False';
					}

					$attribute_name = $this->attributes[$i];

					// Missing/blank cells stay null (treated as a
					// missing value by populateClasses()), instead of
					// being trimmed into an empty string.
					$temp[$attribute_name] = is_null($cell) ? null : trim((string) $cell);
				}

				$result[] = $temp;
			}
		}

		$this->data = $result;

		return $result;
	}

	/**
	 * Load data directly from a CSV file.
	 *
	 * Uses PhpSpreadsheet's dedicated CSV reader instead of relying on
	 * format auto-detection, which can be unreliable for CSV files
	 * (delimiter guessing, encoding, etc.).
	 *
	 * @param  string $path_to_file
	 * @param  string $delimiter
	 * @param  string $enclosure
	 * @param  string $encoding
	 * @return array
	 */
	public function loadCsv(string $path_to_file, string $delimiter = ',', string $enclosure = '"', string $encoding = 'UTF-8'): array
	{
		$this->file = $path_to_file;

		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
		$reader->setDelimiter($delimiter);
		$reader->setEnclosure($enclosure);
		$reader->setInputEncoding($encoding);

		$spreadsheet = $reader->load($path_to_file);

		$data = $this->parseFile($spreadsheet);
		$this->populateClasses();

		return $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setAttributes(array $attributes = array())
	{
		$this->attributes = $attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasAttribute(string $attribute): bool
	{
		return array_search($attribute, $this->attributes) !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAttributes(): ?array
	{
		return $this->attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setData(array $data = array())
	{
		$this->data = $data;
		$this->populateClasses();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getData(int $start = 0, ?int $length = null): array
	{
		if ($length === null)
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
	public function getClasses(array $attributes = array()): array
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
	 * Populate classes and rebuild the attribute-value index used by
	 * countByCriteria() / getByCriteria().
	 */
	protected function populateClasses(): void
	{
		$this->classes = [];
		$this->index = [];

		if (!is_array($this->data))
		{
			return;
		}

		foreach ($this->data as $rowIndex => $row)
		{
			$rowValues = $this->rowAsArray($row);

			if (is_null($rowValues))
			{
				continue;
			}

			foreach ($rowValues as $key => $value)
			{
				if (is_null($value) || $value === '')
				{
					// Missing value: excluded from this attribute's classes
					// and index, so it simply won't match any criteria on
					// this attribute (see the $index docblock above).
					continue;
				}

				if (!array_key_exists($key, $this->classes))
				{
					$this->classes[$key] = [];
				}

				if (array_search($value, $this->classes[$key], true) === false)
				{
					array_push($this->classes[$key], $value);
				}

				$this->index[$key][$value][$rowIndex] = true;
			}
		}
	}

	/**
	 * Normalize a data row (array or object) to an associative array,
	 * or null if it's neither.
	 *
	 * @param  mixed $row
	 * @return array|null
	 */
	private function rowAsArray($row): ?array
	{
		if (is_array($row))
		{
			return $row;
		}

		if (is_object($row))
		{
			return get_object_vars($row);
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getByCriteria(array $criteria = array(), ?int $length = null): array
	{
		$matches = $this->matchingRowIndices($criteria);

		$result = [];
		$count = 0;

		foreach ($matches as $rowIndex => $_)
		{
			if ($length !== null && $count >= $length)
			{
				break;
			}

			$result[] = $this->data[$rowIndex];
			++$count;
		}

		return $result;
	}

	/**
	 * {@inheritdoc}
	 */
	public function countByCriteria(array $criteria = array()): int
	{
		return count($this->matchingRowIndices($criteria));
	}

	/**
	 * Resolve $criteria to the set of matching row indices using the
	 * attribute-value index built by populateClasses(), instead of
	 * scanning every row on every call.
	 *
	 * Unknown criteria attributes (not present in getAttributes()) are
	 * ignored, matching the previous linear-scan behavior.
	 *
	 * @param  array $criteria [{attribute} => {value}]
	 * @return array<int, true> row indices, keyed for fast intersection
	 */
	private function matchingRowIndices(array $criteria): array
	{
		if (empty($criteria))
		{
			return array_fill_keys(array_keys((array) $this->data), true);
		}

		$result = null;

		foreach ($criteria as $key => $value)
		{
			if (!$this->hasAttribute($key))
			{
				continue;
			}

			$bucket = $this->index[$key][$value] ?? [];

			$result = is_null($result) ? $bucket : array_intersect_key($result, $bucket);

			if (empty($result))
			{
				return [];
			}
		}

		// If every criterion attribute was unrecognized (and thus skipped),
		// no constraint was actually applied, so every row matches.
		return $result ?? array_fill_keys(array_keys((array) $this->data), true);
	}
}