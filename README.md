# RandomHex

Cryptographically secure random hex string generation.

## Usage

```php
// PHP 5.6+
use bfrohs\RandomHex\RandomHex::generate as random_hex;

$string = random_hex(32);
```

```php
// PHP 5.5 and lower
use bfrohs\RandomHex\RandomHex;

$string = RandomHex::generate(32);
```

Example `var_dump($string)` output:

> string(32) "112321ec33df8ebc9234ac02dbae4277"
