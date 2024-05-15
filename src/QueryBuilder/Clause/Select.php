<?php

namespace QueryBuilder\Clause;

use \QueryBuilder\Utilities;
use \QueryBuilder\Contract\ClientInterface;
use \QueryBuilder\Contract\ClauseInterface;

class Select implements ClauseInterface {
	/**
	 * @var array $columns
	 */
	private array $columns;

	/**
	 * @param array $columns
	 */
	public function __construct(array $columns) {
		$this->columns = $columns;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getString(ClientInterface $iClient): string {
		return "SELECT " . implode(' ', Utilities::traverse(
			$this->columns,
			[$iClient, "wrap"]
		));
	}
}
