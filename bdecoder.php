<?php

class BDecoder {
	private static function _sum_all($var) {
		$sum = 0;

		switch (gettype($var)) {
			case 'integer':
				$sum += strlen($var) + 2;
			break;

			case 'string':
				$sum += strlen(strlen(($var))) + strlen($var) + 1;
			break;

			case 'array':
				$sum += 2;

				foreach ($var as $key => $value) {
					if (is_string($key)) {
						$sum += self::_sum_all($key);
					}

					$sum += self::_sum_all($value);
				}
			break;
		}

		return $sum;
	}

	private static function _parse_list($str) {
		$_list = array();

		$str = substr($str, 1);	

		while ($str[0] != 'e' && ($item = self::parse($str)) !== false) {
			$_list[] = $item;
		
			$str = substr($str, self::_sum_all($item));
		}

		return (array) $_list;		
	}

	public static function parse($str) {
		switch ($str[0]) {
			case 'l':
				return self::_parse_list($str);

			case 'd':
				$_dict = array();

				$_list = self::_parse_list($str);

				if (count($_list) == 0) {
					return array();
				}

				foreach (array_chunk($_list, 2) as $key) {
					$_dict[$key[0]] = $key[1];
				}

				return (array) $_dict;

			case 'i':
				$marker = strpos($str, 'e');
				$number = substr($str, 1, $marker - 1);

				return (int) $number;

			case '0': case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9':
				$marker = strpos($str, ':');
				$length = substr($str, 0, $marker);
				$data = substr($str, $marker + 1, $length);

				return (string) $data;

			default:
				return false;
		}
	}
}
