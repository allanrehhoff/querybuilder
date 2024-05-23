<?php
use \QueryBuilder\Client\MySQL;
use \QueryBuilder\QueryBuilder;

ini_set("display_errors", 1);

array_key_exists('RUN', $_ENV) or die("Test suite should not be invoked directly, use 'composer run tests' instead\n");

require __DIR__ . "/../autoload.php";

function qb(): \QueryBuilder\QueryBuilder {
	return new QueryBuilder(new MySQL);
}
