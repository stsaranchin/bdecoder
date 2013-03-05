<?php

class BDecoder {
	private static function _ben_array_count(&$item, $key) {
		if (is_string($item)) {
			$item = strlen(strlen($item)) + 1 + strlen($item);

		} elseif (is_int($item)) {
			$item = 1 + strlen($item) + 1;
		}
	}

	public static function parse($str) {
		switch ((string) $str[0]) {
			case 'l':
				$_list = array();

				$str = substr($str, 1);

				while ($str != 'e' && $item = self::parse($str)) {
					$_list[] = $item;

					if (is_string($item) || is_int($item)) {
						$str = substr($str, strlen($item) + strlen(strlen($item)) + 1);

					} else if (is_array($item)) {
						array_walk($item, 'self::_ben_array_count');

						$str = substr($str, 1 + array_sum($item) + 2); 
					}
				}

				return (array) $_list;

			case 'd':
				$_dict = array();
				$_list = array();

				$str = substr($str, 1);

				while ($str != 'e' && $item = self::parse($str)) {
					$_list[] = $item;

					if (is_string($item) || is_int($item)) {
						$str = substr($str, strlen($item) + strlen(strlen($item)) + 1);

					} else if (is_array($item)) {
						array_walk($item, 'self::_ben_array_count');

						$str = substr($str, 1 + array_sum($item) + 2); 
					}
				}

				foreach (array_chunk($_list, 2) as $key) {
					$_dict[$key[0]] = $key[1];
				}

				return (array) $_dict;

			case 'i':
				$marker = strpos($str, 'e');

				return (int) substr($str, 1, $marker - 1);

			case '0': case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9':
				$marker = strpos($str, ':');
				$length = substr($str, 0, $marker);
				$data = substr($str, $marker + 1, $length);

				return (string) substr($data, 0, (int) $length);

			default:
				return false;
		}
	}
}

var_dump(BDecoder::parse('d9:publisher3:bob17:publisher-webpage15:www.example.com18:publisher.location4:home3:intli42ei-13eee'));