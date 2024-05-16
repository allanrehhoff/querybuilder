<?php

use PHPUnit\Framework\TestCase;

class SelectFromTest extends TestCase {
	public function testSelectAllFrom() {
		$expect = "SELECT * FROM `table`";
		$result = qb()->select("*")->from("table")->compose();

		$this->assertEquals($expect, $result);
	}

	public function testSelectColumns() {
		$expect = "SELECT `column1`, `column2`, `column3`";
		$result = qb()->select('column1', 'column2', 'column3')->compose();

		$this->assertEquals($expect, $result);
	}
}
