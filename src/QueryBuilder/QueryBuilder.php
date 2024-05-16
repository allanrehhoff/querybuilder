<?php

namespace QueryBuilder;

use \QueryBuilder\Clause\From;
use \QueryBuilder\Clause\Where;
use \QueryBuilder\Clause\Select;

use \QueryBuilder\Contract\ClientInterface;
use \QueryBuilder\Contract\QueryBuilderInterface;

/**
 * Class QueryBuilder.
 */
class QueryBuilder implements QueryBuilderInterface {
	/**
	 * @var array @params
	 */
	protected array $params = [];

	/**
	 * @var ClientInterface $client
	 */
	protected ClientInterface $client;

	/**
	 * @var \QueryBuilder\Contract\ClauseInterface[]<mixed> $clauses
	 */
	protected array $clauses = [
		'select' => [],
		'from' => [],
		'join' => [],
		'where' => [],
		'groupBy' => [],
		'orderBy' => [],
		'limit' => [],
		'offset' => []
	];

	/**
	 * @param ClientInterface $iClient
	 */
	public function __construct(ClientInterface $iClient) {
		$this->client = $iClient;
	}

	/**
	 * {@inheritdoc}
	 */
	public function select(string ...$columns): self {
		$this->clauses["select"][] = new Select($columns);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function from(string $table): self {
		$this->clauses["from"][] = new From($table);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function where(string $key, string $operator, mixed $value = null): self {
		$this->clauses['where'][] = new Where($key, $operator, $value, Where::LOGIC_WHERE);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function and(string $key, string $operator, mixed $value = null): self {
		$this->clauses['where'][] = new Where($key, $operator, $value, Where::LOGIC_AND);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function or(string $key, string $operator, mixed $value = null): self {
		$this->clauses['where'][] = new Where($key, $operator, $value, Where::LOGIC_OR);
		return $this;
	}

	/**
	 * Add a JOIN clause to the query.
	 *
	 * @param string $table The table to join.
	 * @param string $foreignkey The foreign key to use for the join.
	 * 
	 * @return $this
	 */
	// public function join(string $table, string $foreignkey): self {
	// 	$this->components['join'][] = new Structures\Join($table, $foreignkey);
	// 	return $this;
	// }

	/**
	 * Add a GROUP BY clause to the query.
	 *
	 * @param string|array $columns The column(s) to group by.
	 * 
	 * @return $this
	 */
	// public function groupBy($columns): self {
	// 	$this->components['groupBy'][] = new Structures\GroupBy($columns);
	// 	return $this;
	// }

	/**
	 * Add an ORDER BY clause to the query.
	 *
	 * @param string|array $columns The column(s) to order by.
	 * @param string $direction The sort direction (ASC or DESC).
	 * 
	 * @return $this
	 */
	// public function orderBy($columns, string $direction = 'ASC'): self {
	// 	$this->components['orderBy'][] = new Structures\OrderBy($columns, $direction);
	// 	return $this;
	// }

	/**
	 * Add a LIMIT clause to the query.
	 *
	 * @param int $limit The maximum number of rows to return.
	 * 
	 * @return $this
	 */
	// public function limit(int $limit): self {
	// 	$this->components['limit'][] = new Structures\Limit($limit);
	// 	return $this;
	// }

	/**
	 * Add an OFFSET clause to the query.
	 *
	 * @param int $offset The number of rows to skip.
	 * 
	 * @return $this
	 */
	// public function offset(int $offset): self {
	// 	$this->components['offset'][] = new Structures\Offset($offset);
	// 	return $this;
	// }

	public function getParams(): array {
		return $this->params;
	}

	/**
	 * Generate the SQL query string.
	 *
	 * @return string The SQL query string.
	 */
	public function compose(): string {
		$strings = [];

		foreach ($this->clauses as $clauseType => $clauses) {
			foreach ($clauses as $iClause) {
				$strings[] = $iClause->getString($this->client);
			}
		}

		// Concatenate all query components into the final SQL query
		return implode(' ', $strings);
	}
}
