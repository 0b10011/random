<?php

namespace bfrohs\Random;

class InvalidLengthException extends \InvalidArgumentException {}
class InvalidMinException extends \InvalidArgumentException {}
class InvalidMaxException extends \InvalidArgumentException {}
class UnexpectedMaxOffsetException extends \LogicException {}
class RandomBytesPolyfillException extends \Exception {}
class InsecureServerException extends \Exception {}

class Random {
	/**
	 * Generates cryptographically secure random bytes.
	 * @author Brandon Frohs <bfrohs@gmail.com>
	 * @param  integer $length Number of bytes to return
	 * @return string Returns cryptographically secure bytes
	 * generated using `random_bytes()`.
	 * @throws bfrohs\Random\InvalidLengthException
	 * if `$length` is not a positive integer.
	 * @throws bfrohs\Random\RandomBytesPolyfillException
	 * if the `random_bytes()` polyfill fails for an unknown reason.
	 * @throws bfrohs\Random\InsecureServerException
	 * if the server can't generate cryptographically secure bytes.
	 */
	public static function generateBinary ($length) {
		if (!is_int($length) || $length < 1) {
			throw new InvalidLengthException(
				"Provided length `".serialize($length)."` must be a positive integer"
			);
		}

		try {
			$random_bytes = random_bytes($length);
		} catch (\TypeError $ex) {
			throw new RandomBytesPolyfillException(
				"`random_bytes()` polyfill failed to generate random bytes",
				0,
				$ex
			);
		} catch (\Error $ex) {
			throw new RandomBytesPolyfillException(
				"`random_bytes()` polyfill failed to generate random bytes",
				0,
				$ex
			);
		} catch (\Exception $ex) {
			throw new InsecureServerException(
				"Server could not securely generate random bytes",
				0,
				$ex
			);
		}

		return $random_bytes;
	}

	/**
	 * Generates a cryptographically secure random integer.
	 * @author Brandon Frohs <bfrohs@gmail.com>
	 * @param  integer $min Minimum value of generated integer.
	 * @param  integer $max Maximum value of generated integer.
	 * @return string Returns a cryptographically secure integer
	 * generated using `random_int()`.
	 * @throws bfrohs\Random\InvalidMinException
	 * if `$min` is not an integer.
	 * @throws bfrohs\Random\InvalidMaxException
	 * if `$max` is not an integer.
	 * @throws bfrohs\Random\RandomBytesPolyfillException
	 * if the `random_int()` polyfill fails for an unknown reason.
	 * @throws bfrohs\Random\InsecureServerException
	 * if the server can't generate cryptographically secure bytes.
	 */
	public static function generateInt ($min, $max) {
		if (!is_int($min)) {
			throw new InvalidMinException(
				"Provided minimum `".serialize($min)."` must be an integer"
			);
		} elseif (!is_int($max)) {
			throw new InvalidMaxException(
				"Provided minimum `".serialize($min)."` must be an integer"
			);
		}

		try {
			$random_int = random_int($min, $max);
		} catch (\TypeError $ex) {
			throw new RandomBytesPolyfillException(
				"`random_int()` polyfill failed to generate a random integer",
				0,
				$ex
			);
		} catch (\Error $ex) {
			throw new RandomBytesPolyfillException(
				"`random_int()` polyfill failed to generate a random integer",
				0,
				$ex
			);
		} catch (\Exception $ex) {
			throw new InsecureServerException(
				"Server could not securely generate a random integer",
				0,
				$ex
			);
		}

		return $random_int;
	}

	/**
	 * Generates a cryptographically secure random hex string.
	 * @author Brandon Frohs <bfrohs@gmail.com>
	 * @param  integer $length Length of hex string to return
	 * @return string Returns hex string that is `$length` characters long
	 * and cryptographically secure.
	 * Generated using `bin2hex()` and `random_bytes()`.
	 * @throws bfrohs\Random\InvalidLengthException
	 * if `$length` is not a positive integer.
	 * @throws bfrohs\Random\RandomBytesPolyfillException
	 * if the `random_bytes()` polyfill fails for an unknown reason.
	 * @throws bfrohs\Random\InsecureServerException
	 * if the server can't generate cryptographically secure bytes.
	 */
	public static function generateHex ($length) {
		if (!is_int($length) || $length < 1) {
			throw new InvalidLengthException(
				"Provided length `".serialize($length)."` must be a positive integer"
			);
		}

		// `bin2hex()` converts one byte into two hex characters.
		// In order to get the correct length without getting extra bytes,
		// divide `$length` by 2 and round up.
		$random_bytes = self::generateBinary(intval(ceil($length / 2)));

		// Convert to hex and shorten to `$length` with `substr()`
		return substr(
			bin2hex($random_bytes),
			0,
			$length
		);
	}

	protected static $unreserved_url_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._~';

	/**
	 * Generates a cryptographically secure random string
	 * that is safe for use in URLs (unreserved)
	 * per [RFC 3986 § 2.3](http://www.ietf.org/rfc/rfc3986.txt).
	 * @author Brandon Frohs <bfrohs@gmail.com>
	 * @param  integer $length Length of hex string to return
	 * @return string Returns hex string that is `$length` characters long
	 * and cryptographically secure.
	 * Generated using `bin2hex()` and `random_bytes()`.
	 * @throws bfrohs\Random\InvalidLengthException
	 * if `$length` is not a positive integer.
	 * @throws bfrohs\Random\UnexpectedMaxOffsetException
	 * if the static list of unreserved characters is changed
	 * and no longer fits in the 7-bit range.
	 * @throws bfrohs\Random\RandomBytesPolyfillException
	 * if the `random_bytes()` polyfill fails for an unknown reason.
	 * @throws bfrohs\Random\InsecureServerException
	 * if the server can't generate cryptographically secure bytes.
	 */
	public static function generateUrlSafeString ($length) {
		if (!is_int($length) || $length < 1) {
			throw new InvalidLengthException(
				"Provided length `".serialize($length)."` must be a positive integer"
			);
		}

		// Get the max offset based on the static list of unreserved chars
		$max_offset = strlen(self::$unreserved_url_chars) - 1;
		if ($max_offset >= 128 || $max_offset < 64) {
			throw new UnexpectedMaxOffsetException(
				"\$unreserved_url_chars must fit in the 7-bit range or the bitwise logic below needs to be updated"
			);
		}

		// Generate binary $length bytes long.
		// It should be possible to only read every 7 bits
		// instead of reading 8 and only using 7 of them,
		// but this was easier to implement
		// and isn't a huge issue right now.
		$random_bytes = self::generateBinary($length);

		$str = '';
		for ($i = 0; $i < $length; $i += 1) {
			// Get integer for 7 bits
			$offset = ord($random_bytes{$i}) >> 1;

			// If the offset doesn't exist (66-127),
			// use 6 bits instead.
			if ($offset > $max_offset) {
				$offset = $offset >> 1;
			}

			// Append the unreserved character at `$offset` to `$str`
			$str .= self::$unreserved_url_chars{$offset};
		}

		return $str;
	}
}
