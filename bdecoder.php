<?php

class BDecoder {
	private static function _ben_array_count(&$item, $key) {
		if (is_string($item)) {
			$item = strlen(strlen($item)) + strlen($item) + 1;

		} elseif (is_int($item)) {
			$item = strlen($item) + 2;

		}
	}

	public static function parse($str) {
		switch ($str[0]) {
			case 'l':
				$_list = array();

				$str = substr($str, 1);

				while ($str[0] != 'e' && ($item = self::parse($str)) !== false) {
					$_list[] = $item;

					if (is_string($item)) {
						$str = substr($str, strlen($item) + strlen(strlen($item)) + 1);

					} elseif (is_int($item)) {
						$str = substr($str, strlen((string) $item) + 2);

					} elseif (is_array($item)) {
						array_walk($item, 'self::_ben_array_count');

						$str = substr($str, 1 + array_sum($item) + 1); 
					}
				}

				if (count($_list) == 0) {
					$_list = array(null);
				}

				return (array) $_list;

			case 'd':
				$_dict = array();
				$_list = array();

				$str = substr($str, 1);

				while ($str[0] != 'e' && ($item = self::parse($str)) !== false) {
					$_list[] = $item;

					if (is_string($item))  {
						$str = substr($str, strlen($item) + strlen(strlen($item)) + 1);

					} elseif (is_int($item)) {
						$str = substr($str, strlen($item) + 2);

					} elseif (is_array($item)) {
						array_walk($item, 'self::_ben_array_count');

						$str = substr($str, 1 + array_sum($item) + 1); 
					}
				}

				if (count($_list) == 0) {
					$_dict = array(null);

				} else {
					foreach (array_chunk($_list, 2) as $key) {
						$_dict[$key[0]] = $key[1];
					}
				}

				return (array) $_dict;

			case 'i':
				$marker = strpos($str, 'e');
				$number = (int) substr($str, 1, $marker - 1);

				return $number;

			case '0': case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9':
				$marker = strpos($str, ':');
				$length = substr($str, 0, $marker);
				$data = substr($str, $marker + 1, $length);

				return $data;

			default:
				return false;
		}
	}
}
