<?php

namespace QueryBuilder\Clause;

use \QueryBuilder\Contract\ClientInterface;
use \QueryBuilder\Contract\ClauseInterface;
use \QueryBuilder\Common\Operator;

class Where implements ClauseInterface {
	const TYPE_NULL = "IS NULL";
	const TYPE_NOT_NULL = "IS NOT NULL";
	const TYPE_BETWEEN = "BETWEEN";
	const TYPE_NOT_BETWEEN = "NOT BETWEEN";
	const TYPE_IN = "IN";
	const TYPE_NOT_IN = "NOT IN";

	const LOGIC_WHERE = "WHERE";
	const LOGIC_AND = "AND";
	const LOGIC_OR = "OR";

	private static array $operators = [
		'='           => '=',
		'<'           => '<',
		'>'           => '>',
		'<='          => '<=',
		'>='          => '>=',
		'<>'          => '<>',
		'<=>'         => '<=>',
		'!='          => '!=',
		'LIKE'        => 'LIKE',
		'NOT LIKE'    => 'NOT LIKE',
		'BETWEEN'     => 'BETWEEN',
		'NOT BETWEEN' => 'NOT BETWEEN',
		'ILIKE'       => 'ILIKE',
		'NOT ILIKE'   => 'NOT ILIKE',
		'EXISTS'      => 'EXISTS',
		'NOT EXIST'   => 'NOT EXIST',
		'RLIKE'       => 'RLIKE',
		'NOT RLIKE'   => 'NOT RLIKE',
		'REGEXP'      => 'REGEXP',
		'NOT REGEXP'  => 'NOT REGEXP',
		'MATCH'       => 'MATCH',
		'IS NULL' 	  => 'IS NULL',
		'IS NOT NULL' => 'IS NOT NULL',
		'IN' 		  => 'IN',
		'NOT IN' 	  => 'NOT IN',
		'&'           => '&',
		'|'           => '|',
		'^'           => '^',
		'<<'          => '<<',
		'>>'          => '>>',
		'~'           => '~',
		'~='          => '~=',
		'~*'          => '~*',
		'!~'          => '!~',
		'!~*'         => '!~*',
		'#'           => '#',
		'&&'          => '&&',
		'@>'          => '@>',
		'<@'          => '<@',
		'||'          => '||',
		'&<'          => '&<',
		'&>'          => '&>',
		'-|-'         => '-|-',
		'@@'          => '@@',
		'!!'          => '!!',
	];

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
	 * @var int $parameterCounter
	 */
	private static int $parameterCounter;

	/**
	 * Initialize the column, operator, and value for the SQL clause.
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 */
	public function __construct(string $column, string $operator, mixed $value = null, string $logic) {
		if ($logic !== self::LOGIC_WHERE && $logic !== self::LOGIC_AND && $logic !== self::LOGIC_OR) {
			throw new \InvalidArgumentException(sprintf("%s is not a valid clause", $logic));
		}

		$compare = trim(strtoupper($operator));

		if ($compare === self::TYPE_NULL || $compare === self::TYPE_NOT_NULL) {
			$this->operator = $operator;
			$this->value = null; // Explicitly set value to null
		} elseif ($compare === self::TYPE_BETWEEN || $compare === self::TYPE_NOT_BETWEEN) {
			if (!is_array($value)) {
				throw new \InvalidArgumentException(sprintf("%s expects an array as its value", $compare));
			} else if (count($value) !== 2) {
				throw new \InvalidArgumentException(sprintf("%s expects an array with exactly two indices", $compare));
			}

			$this->operator = $operator;
			$this->value = $value; // Expecting an array with two elements
		} elseif ($compare === self::TYPE_IN || $compare === self::TYPE_NOT_IN) {
			if (!is_array($value)) {
				throw new \InvalidArgumentException(sprintf("%s expects an array as its value", $compare));
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

		isset(self::$operators[$this->operator]) || throw new \InvalidArgumentException(sprintf("Operator '%s' is not allowed.", $this->operator));

		$this->column = $column;
		$this->logic = $logic;

		self::$parameterCounter = 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getString(ClientInterface $iClient): string {
		$string = $this->logic . ' ';

		if ($this->value === null) {
			$string .= $iClient->wrap($this->column) . ' ' . $this->operator;
		} else if ($this->operator === self::TYPE_BETWEEN || $this->operator === self::TYPE_NOT_BETWEEN) {
			//[$start, $end] = $this->value;
			$string .= $iClient->wrap($this->column) . ' ' . $this->operator . ' :val' . static::$parameterCounter++ . ' ' . self::LOGIC_AND . ' :val' . static::$parameterCounter++;
		} else if ($this->operator == self::TYPE_IN || $this->operator == self::TYPE_NOT_IN) {
			$list = [];

			foreach ($this->value as $item) {
				$key = "val" . static::$parameterCounter++;
				$list[$key]  = $item;
			}

			// (:val0, :val1, :val2)
			$in = "(:" . implode(", :", array_keys($list)) . ')';
			$string .= $iClient->wrap($this->column) . ' ' . $this->operator . ' ' . $in;
		} else {
			$string .= $iClient->keys(
				$this->column,
				$this->operator
			);
		}

		return $string;
	}
}
