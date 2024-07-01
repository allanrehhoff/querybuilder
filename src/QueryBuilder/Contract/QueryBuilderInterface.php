<?php

namespace QueryBuilder\Contract;

/**
 * Interface for QueryBuilderInterface.
 */
interface QueryBuilderInterface {
	/**
	 * Set the columns to select
	 *
	 * @param string $columns The table name.
	 * @return $this
	 */
	public function select(string ...$columns): self;

	/**
	 * Set the table to perform the query on.
	 *
	 * @param string $table The table name.
	 * @return $this
	 */
	public function from(string $table): self;

	/**
	 * Add a WHERE clause to the query.
	 *
	 * @param string $key The column name.
	 * @param string $operator The comparison operator.
	 * @param mixed $value The value to compare against.
	 * @return $this
	 */
	public function where(string $key, string $operator, mixed $value = null): self;

	/**
	 * Add a AND clause to the query.
	 *
	 * @param string $key The column name.
	 * @param string $operator The comparison operator.
	 * @param mixed $value The value to compare against.
	 * @return $this
	 */
	public function and(string $key, string $operator, mixed $value = null): self;

	/**
	 * Add a OR clause to the query.
	 *
	 * @param string $key The column name.
	 * @param string $operator The comparison operator.
	 * @param mixed $value The value to compare against.
	 * @return $this
	 */
	public function or(string $key, string $operator, mixed $value = null): self;

	/**
	 * Add a JOIN clause to the query.
	 *
	 * @param string $table The table to join.
	 * @param string $foreignkey The foreign key to use for the join.
	 * @return $this
	 */
	// public function join(string $table, string $foreignkey): self;

	/**
	 * Add a GROUP BY clause to the query.
	 *
	 * @param string|array $columns The column(s) to group by.
	 * @return $this
	 */
	// public function groupBy($columns): self;

	/**
	 * Add an ORDER BY clause to the query.
	 *
	 * @param string|array $columns The column(s) to order by.
	 * @param string $direction The sort direction (ASC or DESC).
	 * @return $this
	 */
	// public function orderBy($columns, string $direction = 'ASC'): self;

	/**
	 * Add a LIMIT clause to the query.
	 *
	 * @param int $limit The maximum number of rows to return.
	 * @return $this
	 */
	// public function limit(int $limit): self;

	/**
	 * Add an OFFSET clause to the query.
	 *
	 * @param int $offset The number of rows to skip.
	 * @return $this
	 */
	// public function offset(int $offset): self;

	/**
	 * Get the parameters for prepared statement.
	 *
	 * @return array The parameters.
	 */
	public function params(): array;

	/**
	 * Generate the SQL query string.
	 * @return string The SQL query string.
	 */
	public function compose(): string;
}
