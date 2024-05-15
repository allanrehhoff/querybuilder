<?php

namespace QueryBuilder\Clause;

use \QueryBuilder\Contract\ClientInterface;
use \QueryBuilder\Contract\ClauseInterface;

class From implements ClauseInterface {
	/**
	 * @var string $table
	 */
	private string $table;

	/**
	 * @param string $table
	 */
	public function __construct(string $table) {
		$this->table = $table;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getString(ClientInterface $iClient): string {
		return "FROM " . $iClient->wrap($this->table);
	}
}
