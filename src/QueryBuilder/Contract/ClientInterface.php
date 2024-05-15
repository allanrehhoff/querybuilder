<?php

namespace QueryBuilder\Contract;

interface ClientInterface {
	/**
	 * @param string $identifier
	 * @return string
	 */
	public function wrap(string|array $identifier): string;

	/**
	 * @param string $column
	 * @param string $operator
	 * @param string $variablePrefix
	 * @return string
	 */
	public function keys(string $column, string $operator, string $variablePrefix = ""): string;
}
