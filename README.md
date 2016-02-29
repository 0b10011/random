# Random

Cryptographically secure random data generation.

## Usage

### Hex string

```php
use bfrohs\Random\Random;

$string = Random::generateHex(32);

var_dump($string);
```

> string(32) "112321ec33df8ebc9234ac02dbae4277"
