<?php

declare(strict_types=1);

namespace Algorithm\C45;

class TreeNode
{
	/**
	 * self parent's TreeNode
	 * 
	 * @var Algorithm\C45\TreeNode
	 */
	protected $parent;

	/**
	 * attribute name
	 * 
	 * @var string
	 */
	protected $attribute;

	/**
	 * attribute's values
	 *  
	 * @var array
	 */
	protected $values;

	/**
	 * classes count for this node and it's child
	 * 
	 * @var array
	 */
	protected $classes_count;

	/**
	 * @var boolean
	 */
	protected $is_leaf = false;

	/**
	 * Set parent
	 * 
	 * @param TreeNode $parent
	 */
	public function setParent(TreeNode $parent): void
	{
		$this->parent = $parent;
	}

	/**
	 * Get parent
	 * 
	 * @return Algorithm\C45\TreeNode|null
	 */
	public function getParent(): ?self
	{
		return $this->parent;
	}

	/**
	 * Set attribute name
	 * 
	 * @param string $attribute
	 */
	public function setAttribute(string $attribute): void
	{
		$this->attribute = $attribute;
	}

	/**
	 * Add classes count
	 * 
	 * @param string $valueName
	 * @param array  $classesCount
	 */
	public function addClassesCount(string $valueName, array $classesCount = array()): void
	{
		$this->classes_count[$valueName] = $classesCount;
	}

	/**
	 * Set is leaf
	 * 
	 * @param boolean $is_leaf
	 * @return Algorithm\C45\TreeNode 
	 */
	public function setIsLeaf(bool $is_leaf = false): self
	{
		$this->is_leaf = $is_leaf;
		return $this;
	}

	/**
	 * Get is leaf
	 * 
	 * @return boolean
	 */
	public function getIsLeaf(): bool
	{
		return $this->is_leaf;
	}

	/**
	 * Add child TreeNode
	 * 
	 * @param string $value
	 * @param mixed $child
	 */
	public function addChild($value, $child)
	{
		if (!isset($this->values))
		{
			$this->values = [];
		}

		$this->values[$value] = $child;
		return $this;
	}

	/**
	 * Get child
	 * 
	 * @param  string $value
	 * @return Algorithm\C45\TreeNode
	 */
	public function getChild($value)
	{
		if ($this->hasValue($value))
		{
			return $this->values[$value];
		}
	}

	/**
	 * Get values
	 * 
	 * @return array current of node value
	 */
	public function getValues(): array
	{
		return array_keys($this->values);
	}

	/**
	 * Get attribute name
	 * 
	 * @return string
	 */
	public function getAttributeName(): ?string
	{
		return $this->attribute;
	}

	/**
	 * Remove value from TreeNode
	 * 
	 * @param  string $value
	 * @return Algorithm\C45\TreeNode
	 */
	public function removeValue($value): self
	{
		if ($this->hasValue($value))
		{
			unset($this->values[$value]);
		}

		return $this;
	}

	/**
	 * Check value in current node
	 * 
	 * @param  string  $value
	 * @return boolean
	 */
	public function hasValue($value): bool
	{
		if (!isset($this->values))
		{
			return false;
		}

		return array_key_exists($value, $this->values);
	}

	/**
	 * Classify data.
	 *
	 * If this node's split attribute is missing from $data (key absent,
	 * or explicitly null/empty), classification falls back to the branch
	 * that had the most training instances at this node (the standard
	 * C4.5 strategy for handling missing values at prediction time),
	 * rather than silently returning null.
	 * 
	 * @param  array  $data
	 * @return string
	 */
	public function classify(array $data): mixed
	{
		$attrValue = $data[$this->attribute] ?? null;

		if (is_null($attrValue) || $attrValue === '')
		{
			return $this->classifyWithMissingValue($data);
		}

		if (!$this->hasValue($attrValue))
		{
			return 'unclassified';
		}

		return $this->classifyBranch($this->values[$attrValue], $data);
	}

	/**
	 * Classify $data using the majority branch (the value with the most
	 * training instances) when the split attribute's value is missing.
	 *
	 * @param  array $data
	 * @return string
	 */
	private function classifyWithMissingValue(array $data): mixed
	{
		$majorityValue = $this->getMajorityBranchValue();

		if (is_null($majorityValue) || !$this->hasValue($majorityValue))
		{
			return 'unclassified';
		}

		return $this->classifyBranch($this->values[$majorityValue], $data);
	}

	/**
	 * @param  TreeNode $child
	 * @param  array    $data
	 * @return string
	 */
	private function classifyBranch(TreeNode $child, array $data): mixed
	{
		if (!$child->getIsLeaf())
		{
			return $child->classify($data);
		}

		return $child->getChild('result');
	}

	/**
	 * The attribute value (branch) with the most training instances at
	 * this node, based on the counts recorded via addClassesCount().
	 *
	 * @return string|null
	 */
	private function getMajorityBranchValue(): mixed
	{
		if (empty($this->classes_count))
		{
			return null;
		}

		$totals = [];

		foreach ($this->classes_count as $value => $counts)
		{
			$totals[$value] = array_sum($counts);
		}

		arsort($totals);

		return array_key_first($totals);
	}

	/**
	 * 
	 * Draw tree with array
	 * 
	 * @return array
	 */
	public function toArray(): array
	{
		$data = [];
		$data['attribute'] = $this->attribute;
		foreach ($this->values as $key => $value)
		{
			if (!is_null($value))
			{
				if ($value instanceof self)
				{
					$data['values'][$key] = $value->toArray();
				}
			}
		}

		return $data;
	}

	/**
	 * 
	 * Draw tree with array
	 * 
	 * @return array
	 */
	public function toJson(): string
	{
		$data = [];
		$data['attribute'] = $this->attribute;
		foreach ($this->values as $key => $value)
		{
			if (!is_null($value))
			{
				if ($value instanceof self)
				{
					$data['values'][$key] = $value->toJson();
				}
			}
		}

		return json_encode($data);
	}

	/**
	 * Draw tree with string
	 * 
	 * @param  string $tabs
	 * @return string
	 */
	public function toString(string $tabs = ''): string
	{
		$result = '';

		foreach ($this->values as $key => $child)
		{
			$result .= $tabs.$this->attribute.' = '.$key;

			if ($child->getIsLeaf())
			{
				$classCount = $this->getInstanceCountAsString($key);
				$result .= ' : '.$child->getChild('result').' '.$classCount."\n";
			}
			else 
			{
				$result .= "\n";
				$result .= $child->toString($tabs."|\t");
			}
		}

		return $result;
	}

	/**
	 * Export tree to Graphviz DOT format, for visualization
	 * with tools such as https://dreampuf.github.io/GraphvizOnline/
	 * or the `dot` CLI (e.g. `dot -Tpng tree.dot -o tree.png`).
	 *
	 * @param  string $tabs
	 * @param  string $node_id   internal, used for recursion
	 * @param  boolean $is_root  internal, used for recursion
	 * @return string
	 */
	public function toDot(string $tabs = "\t", string $node_id = 'n0', bool $is_root = true): string
	{
		$lines = [];

		if ($is_root)
		{
			$lines[] = 'digraph C45Tree {';
			$lines[] = $tabs . 'node [shape=box, fontname="Helvetica"];';
			$lines[] = $tabs . 'edge [fontname="Helvetica"];';
		}

		$safe_attribute = $this->escapeDotLabel((string) $this->attribute);
		$lines[] = $tabs . $node_id . ' [label="' . $safe_attribute . '"];';

		$i = 0;

		foreach ($this->values as $value => $child)
		{
			if (is_null($child) || !($child instanceof self))
			{
				continue;
			}

			$child_id = $node_id . '_' . $i;
			$safe_value = $this->escapeDotLabel((string) $value);

			if ($child->getIsLeaf())
			{
				$label = $this->escapeDotLabel((string) $child->getChild('result'));
				$lines[] = $tabs . $child_id . ' [label="' . $label . '", shape=ellipse, style=filled, fillcolor="#d9ead3"];';
				$lines[] = $tabs . $node_id . ' -> ' . $child_id . ' [label="' . $safe_value . '"];';
			}
			else
			{
				$lines[] = $tabs . $node_id . ' -> ' . $child_id . ' [label="' . $safe_value . '"];';
				$lines[] = $child->toDot($tabs, $child_id, false);
			}

			++$i;
		}

		if ($is_root)
		{
			$lines[] = '}';
		}

		return implode("\n", $lines);
	}

	/**
	 * Escape a string for safe use inside a DOT label.
	 *
	 * @param  string $value
	 * @return string
	 */
	private function escapeDotLabel(string $value): string
	{
		return str_replace(['"', "\n"], ['\\"', ' '], $value);
	}

	/**
	 * Get classes count as string
	 * 
	 * @param  string $attribute_value
	 * @return string
	 */
	private function getClassesCountAsString($attribute_value): string
	{
		$result = '(';
		$total = array_sum($this->classes_count[$attribute_value]);

		foreach ($this->classes_count[$attribute_value] as $key => $value) {
			$result .= $value.'/';
		}

		$result .= $total.')';

		return $result;
	}

	/**
	 * Get instance count as string
	 * 
	 * @param  string $attribute_value string
	 * @return string
	 */
	private function getInstanceCountAsString($attribute_value): string
	{
		$result = '(';
		$total = array_sum($this->classes_count[$attribute_value]);
		$child = $this->getChild($attribute_value);
		$className = $child->getChild('result');
		$classCount = $this->classes_count[$attribute_value][$className];

		if ($total > $classCount) {
			$result .= $total.'.0';
			$result .= '/'.($total - $classCount).'.0';
		} else {
			$result .= $classCount.'.0';
		}

		$result .= ')';

		return $result;
	}
}