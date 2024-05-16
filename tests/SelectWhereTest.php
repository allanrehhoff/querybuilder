<?php

use PHPUnit\Framework\TestCase;

class SelectWhereTest extends TestCase {
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

	public function testSelectWhereAndEquals() {
		$expect = "SELECT * FROM `table` WHERE `column1` = :column1 AND `column2` = :column2";
		$result = qb()->select("*")->from("table")->where("column1", 1)->and("column2", 2)->compose();

		$this->assertEquals($expect, $result);
	}

	public function testWhereAndOrEquals() {
		$expect = "SELECT * FROM `table` WHERE `column1` = :column1 AND `column2` = :column2 OR `column3` = :column3";
		$result = qb()->select("*")->from("table")->where("column1", 1)->and("column2", 2)->or("column3", 3)->compose();

		$this->assertEquals($expect, $result);
	}
}