<?php

namespace QueryBuilder\Clause;

use \QueryBuilder\Contract\ClientInterface;
use \QueryBuilder\Contract\ClauseInterface;

class Where implements ClauseInterface {
	const TYPE_NULL = "IS NULL";
	const TYPE_NOT_NULL = "IS NOT NULL";
	const TYPE_BETWEEN = "BETWEEN";
	const TYPE_IN = "IN";
	const TYPE_NOT_IN = "NOT IN";

	const LOGIC_WHERE = "WHERE";
	const LOGIC_AND = "AND";
	const LOGIC_OR = "OR";

	/**
	 * @var string $logic
	 */
	private string $logic;

	/**
	 * @var string $column
	 */
	private string $column;

	/**
	 * @var string $operator
	 */
	private string $operator;

	/**
	 * @var mixed $value
	 */
	private mixed $value;

	/**
	 * @var int $arrayINCounter
	 */
	private int $arrayINCounter;

	/**
	 * Initialize the column, operator, and value for the SQL clause.
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 */
	public function __construct(string $column, string $operator, mixed $value = null, string $logic) {
		if ($logic !== self::LOGIC_WHERE && $logic !== self::LOGIC_AND && $logic !== self::LOGIC_OR) {
			throw new \InvalidArgumentException(sprintf("%s is not a valid clause", $logic));
		}

		$operator = trim(strtoupper($operator));

		if ($operator === self::TYPE_NULL || $operator === self::TYPE_NOT_NULL) {
			$this->operator = $operator;
			$this->value = null; // Explicitly set value to null
		} elseif ($operator === self::TYPE_BETWEEN) {
			if (!is_array($value)) {
				throw new \InvalidArgumentException(sprintf("%s expects an array as its value", $operator));
			} else if (count($value) !== 2) {
				throw new \InvalidArgumentException(sprintf("%s expects an array with exactly two indices", $operator));
			}

			$this->operator = $operator;
			$this->value = $value; // Expecting an array with two elements
		} elseif ($operator === self::TYPE_IN || $operator === self::TYPE_NOT_IN) {
			if (!is_array($value)) {
				throw new \InvalidArgumentException(sprintf("%s expects an array as its value", $operator));
			}

			$this->operator = $operator;
			$this->value = $value; // Expecting an array
		} else {
			if ($value === null) {
				$this->operator = '=';
				$this->value = $operator;
			} else {
				$this->operator = $operator;
				$this->value = $value;
			}
		}

		$this->column = $column;
		$this->logic = $logic;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getString(ClientInterface $iClient): string {
		//$column = $this->column;
		$string = $this->logic . ' ';

		if ($this->value === null) {
			$string .= $iClient->wrap($this->column) . ' ' . $this->operator;
		} else if ($this->operator === self::TYPE_BETWEEN) {
			[$start, $end] = $this->value;
			$string .= $iClient->wrap($this->column) . ' ' . self::TYPE_BETWEEN . ' ' . $start . " " . self::LOGIC_AND . " " . $end;
		} else if ($this->operator == self::TYPE_IN || $this->operator == self::TYPE_NOT_IN) {
			$list = [];

			foreach ($this->value as $item) {
				$key = "val" . $this->arrayINCounter++;
				$list[$key]  = $item;
				//$this->filters[$key] = $item;
			}

			// (:val0, :val1, :val2)
			$in = "(:" . implode(", :", array_keys($list)) . ')';
			$string .= self::TYPE_IN . ' ' . $in;
		} else {
			$string .= $iClient->keys(
				$this->column,
				$this->operator
			);
		}

		return $string;
	}
}
