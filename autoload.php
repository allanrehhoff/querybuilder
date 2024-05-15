<?php

use \QueryBuilder\QueryBuilder;

spl_autoload_register(function (string $class): void {
	$path = [
		__DIR__,
		"src",
		str_replace("\\", DIRECTORY_SEPARATOR, $class)
	];

	$filename = implode(DIRECTORY_SEPARATOR, $path) . ".php";

	if (is_readable($filename) && str_starts_with($class, "QueryBuilder")) {
		require_once $filename;
	}
}, true, true);
