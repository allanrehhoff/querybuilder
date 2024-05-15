<?php
use \QueryBuilder\Client\MySQL;
use \QueryBuilder\QueryBuilder;

ini_set("display_errors", 1);
error_reporting(E_ALL);

require __DIR__ . "/../autoload.php";

function qb(): \QueryBuilder\QueryBuilder {
	return new QueryBuilder(new MySQL);
}
