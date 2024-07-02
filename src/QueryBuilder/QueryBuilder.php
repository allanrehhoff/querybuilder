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
		'having' => [],
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
	 * {@inheritdoc}
	 */
	public function having(string $key, string $operator, mixed $value = null): self {
		$this->clauses['having'][] = new Where($key, $operator, $value, Where::LOGIC_HAVING);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	// public function join(string $table, string $foreignkey): self {
	// 	$this->components['join'][] = new Structures\Join($table, $foreignkey);
	// 	return $this;
	// }

	/**
	 * {@inheritdoc}
	 */
	// public function groupBy($columns): self {
	// 	$this->components['groupBy'][] = new Structures\GroupBy($columns);
	// 	return $this;
	// }

	/**
	 * {@inheritdoc}
	 */
	// public function orderBy($columns, string $direction = 'ASC'): self {
	// 	$this->components['orderBy'][] = new Structures\OrderBy($columns, $direction);
	// 	return $this;
	// }

	/**
	 * {@inheritdoc}
	 */
	// public function limit(int $limit): self {
	// 	$this->components['limit'][] = new Structures\Limit($limit);
	// 	return $this;
	// }

	/**
	 * {@inheritdoc}
	 */
	// public function offset(int $offset): self {
	// 	$this->components['offset'][] = new Structures\Offset($offset);
	// 	return $this;
	// }

	/**
	 * {@inheritdoc}
	 */
	public function params(): array {
		$params = [];

		foreach ($this->clauses["where"] as $iClause) {
			$params = array_merge(
				$params,
				$iClause->getParams()
			);
		}

		return $params;
	}

	/**
	 * {@inheritdoc}
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
