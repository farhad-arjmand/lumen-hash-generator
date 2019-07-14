# 2. Configuration

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)
  
## Settings

### Default:

```php
<?php

// config/hash.php

return [

	/* -----------------------------------------------------------------
	 |  Hash Algorithm
	 | -----------------------------------------------------------------
	 | Supported: 'md5', 'sha1', 'sha256', 'sha512', 'gost'
	 | Default: 'sha1'
	 */
	'algo' => 'sha1',
	/* -----------------------------------------------------------------
	 |  Hash Raw Output
	 | -----------------------------------------------------------------
	 | Specifies hex or binary output format.
	 | Supported: 'binary', 'hex'
	 | Default: 'hex'
	 */
	'raw' => 'hex',
	/* -----------------------------------------------------------------
	 |  Hash Salt
	 | -----------------------------------------------------------------
	 | Random string to generate stronger hash. Ex: "f1nd1ngn3m0"
	 */
	'salt' => '',
	/* -----------------------------------------------------------------
	 |  Token Expiration time (in seconds)
	 | -----------------------------------------------------------------
	 | Default: 60
	 */
	'jwt-leeway' => 60,

];
```

You can specify here your default hash algorithm that you would to use.

### JWT Secret key

Update your .env file with something like:

`JWT_SECRET=foobar`

It is the key that will be used to sign your tokens.