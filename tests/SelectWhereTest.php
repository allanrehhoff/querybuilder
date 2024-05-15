<?php

use PHPUnit\Framework\TestCase;

class SelectWhereTest extends TestCase {
	public function testSelectFrom() {
		$expect = "SELECT * FROM `table`";
		$result = qb()->select("*")->from("table")->compose();

		$this->assertEquals($expect, $result);
	}

	public function testWhereEquals() {
		$expect = "SELECT * FROM `table` WHERE `column` = :column";
		$result = qb()->select("*")->from("table")->where("column", "value")->compose();

		$this->assertEquals($expect, $result);
	}

	public function testWhereGreaterThan() {
		$expect1 = "SELECT * FROM `table` WHERE `column` > :column";
		$result1 = qb()->select("*")->from("table")->where("column", ">", "value")->compose();

		$expect2 = "SELECT * FROM `table` WHERE `column` >= :column";
		$result2 = qb()->select("*")->from("table")->where("column", ">=", "value")->compose();

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}

	public function testWhereLessThan() {
		$expect1 = "SELECT * FROM `table` WHERE `column` < :column";
		$result1 = qb()->select("*")->from("table")->where("column", "<", "value")->compose();

		$expect2 = "SELECT * FROM `table` WHERE `column` <= :column";
		$result2 = qb()->select("*")->from("table")->where("column", "<=", "value")->compose();

		$this->assertEquals($expect1, $result1);
		$this->assertEquals($expect2, $result2);
	}

	public function testWhereNull() {
		$expect = "SELECT * FROM `table` WHERE `column` IS NULL";
		$result = qb()->select("*")->from("table")->where("column", "IS NULL")->compose();

		$this->assertEquals($expect, $result);
	}

	public function testWhereNotNull() {
		$expect = "SELECT * FROM `table` WHERE `column` IS NOT NULL";
		$result = qb()->select("*")->from("table")->where("column", "IS NOT NULL")->compose();

		$this->assertEquals($expect, $result);
	}
}
