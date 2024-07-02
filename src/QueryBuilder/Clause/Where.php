<?php

namespace QueryBuilder\Clause;

use \QueryBuilder\Contract\ClientInterface;
use \QueryBuilder\Contract\ClauseInterface;
use \QueryBuilder\Common\Operator;
use \QueryBuilder\QueryBuilder;

class Where implements ClauseInterface {
	const TYPE_NULL = "IS NULL";
	const TYPE_NOT_NULL = "IS NOT NULL";
	const TYPE_BETWEEN = "BETWEEN";
	const TYPE_NOT_BETWEEN = "NOT BETWEEN";
	const TYPE_IN = "IN";
	const TYPE_NOT_IN = "NOT IN";
	const TYPE_EXISTS = "EXISTS";
	const TYPE_NOT_EXISTS = "NOT EXISTS";

	const LOGIC_WHERE = "WHERE";
	const LOGIC_AND = "AND";
	const LOGIC_OR = "OR";
	const LOGIC_HAVING = "HAVING";

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
		'NOT EXISTS'  => 'NOT EXISTS',
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
	 * Parameters used by this clause
	 *
	 * @var array
	 */
	private array $params = [];

	/**
	 * @var int $parameterCounter
	 */
	private static int $parameterCounter = 0;

	/**
	 * Initialize the column, operator, and value for the SQL clause.
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 */
	public function __construct(string $column, string $operator, mixed $value = null, string $logic) {
		if ($logic !== self::LOGIC_WHERE && $logic !== self::LOGIC_AND && $logic !== self::LOGIC_OR && $logic !== self::LOGIC_HAVING) {
			throw new \InvalidArgumentException(sprintf("%s is not a valid clause", $logic));
		}

		$compare = trim(strtoupper($operator));

		if($logic == self::LOGIC_WHERE) {
			static::$parameterCounter = 0;
		}

		if ($value instanceof QueryBuilder) {
            $this->operator = $operator;
            $this->value = '(' . $value->compose() . ')'; // Wrap subquery in parentheses
			$params = $value->params();
        } else if ($compare === self::TYPE_NULL || $compare === self::TYPE_NOT_NULL) {
			$this->operator = $operator;

			// Explicitly set value to null
			$this->value = null;
			$params = [$column => null];
		} elseif ($compare === self::TYPE_BETWEEN || $compare === self::TYPE_NOT_BETWEEN) {
			$params = [];
			$between = [];

			if (!is_array($value)) {
				throw new \InvalidArgumentException(sprintf("%s expects an array as its value", $compare));
			} else if (count($value) !== 2) {
				throw new \InvalidArgumentException(sprintf("%s expects an array with exactly two indices", $compare));
			}

			foreach ($value as $item) {
				$key = "val" . static::$parameterCounter++;
				$between[] = ':' . $key; 
				$params[$key]  = $item;
			}

			$value = implode(' ' . self::LOGIC_AND . ' ', $between);
			
			$this->operator = $operator;
			$this->value = $value; // Expecting an array with two elements
		} elseif ($compare === self::TYPE_IN || $compare === self::TYPE_NOT_IN) {
			$params = [];

			if (!is_array($value)) {
				throw new \InvalidArgumentException(sprintf("%s expects an array as its value", $compare));
			}

			foreach ($value as $item) {
				$key = "val" . static::$parameterCounter++;
				$params[$key]  = $item;
			}

			// (:val0, :val1, :val2)
			$in = "(:" . implode(", :", array_keys($params)) . ')';

			$this->operator = $operator;
			$this->value = $in; // Expecting an array
		} else {
			if ($value === null) {
				$this->operator = '=';
				$this->value = $operator;
			} else {
				$this->operator = $operator;
				$this->value = $value;
			}

			$params = [$column => $this->value];
		}

		isset(self::$operators[$this->operator]) || throw new \InvalidArgumentException(sprintf("Operator '%s' is not allowed.", $this->operator));

		$this->params = array_merge($this->params, $params);
		$this->column = $column;
		$this->logic = $logic;
	}

	/**
	 * Get parameters related to this clause
	 * 
	 * @return array
	 */
	public function getParams(): array {
		return $this->params;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getString(ClientInterface $iClient): string {
		$string = $this->logic . ' ';

		if ($this->value === null) {
			$string .= $iClient->wrap($this->column) . ' ' . $this->operator;
		} else if ($this->operator === self::TYPE_BETWEEN || $this->operator === self::TYPE_NOT_BETWEEN) {
			$string .= $iClient->wrap($this->column) . ' ' . $this->operator . ' ' . $this->value;
		} else if ($this->operator == self::TYPE_IN || $this->operator == self::TYPE_NOT_IN) {
			$string .= $iClient->wrap($this->column) . ' ' . $this->operator . ' ' . $this->value;
		} else if($this->operator == self::TYPE_EXISTS || $this->operator == self::TYPE_NOT_EXISTS) {
			$string .= $this->operator . ' ' . $this->value;
		} else {
			$string .= $iClient->keys(
				$this->column,
				$this->operator
			);
		}

		return $string;
	}
}
