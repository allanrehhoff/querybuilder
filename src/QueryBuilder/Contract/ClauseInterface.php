<?php

namespace QueryBuilder\Contract;

use \QueryBuilder\Contract\ClientInterface;

interface ClauseInterface {
	/**
	 * @param ClientInterface $iClient
	 * @return string
	 */
	public function getString(ClientInterface $iClient): string;
}
