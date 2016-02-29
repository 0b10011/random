# Random

Cryptographically secure random data generation.

## Usage

### Hex string

Random string of hexadecimal characters (`[0-9a-f]`).

```php
use bfrohs\Random\Random;

$string = Random::generateHex(32);

var_dump($string);
```

> string(32) "112321ec33df8ebc9234ac02dbae4277"

### Url-safe string

Random string that is safe for use in URLs (unreserved characters)
per [RFC 3986 § 2.3](http://www.ietf.org/rfc/rfc3986.txt)
(`[a-zA-Z0-9]`, `_`, `~`, `-`, and `.`).

```php
use bfrohs\Random\Random;

$string = Random::generateUrlSafeString(32);

var_dump($string);
```

> string(32) "XQ_7J495ZWd3s~5TWz-FNFiPkeM3z9K."

### Binary

Random bytes.

```php
use bfrohs\Random\Random;

$string = Random::generateBinary(32);

// Note the use of `bin2hex()` to make output readable
var_dump(bin2hex($string));
```

> string(64) "68a74f84be34c9be12ecada360e91639fd0d41cfae368d90fe8cc4c4ff66eed3"

### Integer

Random integer.

```php
use bfrohs\Random\Random;

$string = Random::generateInt(0, 29524);

var_dump($string);
```

> int(12860)
