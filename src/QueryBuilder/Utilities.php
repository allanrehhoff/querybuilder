<?php

namespace QueryBuilder;

class Utilities {

	/**
	 * Applies callback function to all items in an array
	 * Similar to array_walk, but returns instead
	 * @param array &$items
	 * @param callable $callback
	 * @return array
	 */
	public static function traverse(array &$items, callable $callback): array {
		foreach ($items as $key => $value) {
			$items[$key] == $callback($value);
		}

		return $items;
	}
}
