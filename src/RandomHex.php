<?php

namespace bfrohs\RandomHex;

class InvalidLengthException extends \InvalidArgumentException {}
class RandomBytesPolyfillException extends \Exception {}
class InsecureServerException extends \Exception {}

class RandomHex {
	/**
	 * Generates a cryptographically secure random hex string.
	 * @author Brandon Frohs <bfrohs@gmail.com>
	 * @param  integer $length Length of hex string to return
	 * @return string Returns hex string that is `$length` characters long
	 * and cryptographically secure.
	 * Generated using `bin2hex()` and `random_bytes()`.
	 * @throws bfrohs\RandomHex\InvalidLengthException
	 * if `$length` is not a positive integer.
	 * @throws bfrohs\RandomHex\RandomBytesPolyfillException
	 * if the `random_bytes()` polyfill fails for an unknown reason.
	 * @throws bfrohs\RandomHex\InsecureServerException
	 * if the server can't generate cryptographically secure bytes.
	 */
	public static function generate ($length) {
		if (!is_int($length) || $length < 1) {
			throw new InvalidLengthException(
				"Provided length `".serialize($length)."` must be a positive integer"
			);
		}

		// `bin2hex()` converts one byte into two hex characters.
		// In order to get the correct length without getting extra bytes,
		// divide `$length` by 2 and round up.
		try {
			$bytes_length = ceil($length / 2);
			$random_bytes = random_bytes($bytes_length);
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

		// Convert to hex and shorten to `$length` with `substr()`
		return substr(
			bin2hex($random_bytes),
			0,
			$length
		);
	}
}
