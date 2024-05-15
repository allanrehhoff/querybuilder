<?php

namespace QueryBuilder\Client;

use \QueryBuilder\Contract\ClientInterface;

class MySQL implements ClientInterface {

	/**
	 * {@inheritdoc}
	 */
	public function wrap(string|array $identifier): string {
		if ($identifier === '*') {
			return $identifier;
		}

		$parts = explode('.', $identifier);

		foreach ($parts as &$part) {
			if ($part === '*') continue;
			$part = '`' . str_replace('`', '\\`', $part) . '`';
		}

		return implode('.', $parts);
	}

	/**
	 * {@inheritdoc}
	 */
	public function keys(string $column, string $operator, string $variablePrefix = ""): string {
		if ($column == null) return "1";
		return $this->wrap($column) . " " . $operator . " :" . $variablePrefix . $column;
	}
}
