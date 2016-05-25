php-gpg
=======

**This project is currently not actively maintained, please take a look at https://github.com/singpolyma/openpgp-php as alternative.**

php-gpg is a pure PHP implementation of GPG/PGP (currently supports encryption only).  The library does not require PGP/GPG binaries and should run on any platform that supports PHP.

This library is useful for encrypting data before it is sent over an insecure protocol (for example email).  Messages encrypted with this library are compatible and can be decrypted by standard GPG/PGP clients.

Features/Limitations
--------------------

 * Supports RSA, DSA public key length of 2,4,8,16,512,1024,2048 or 4096
 * Currently supports only encrypt
 * Encrypted messages are integrity protected (php-gpg 1.6.0+)

Hey You!  If you have a good understanding of public key encryption and want to implement signing or decryption your pull request would be welcome.

Composer
-----
If you use PHP < 7.0, please install random_compat with Composer.

Just run `php composer.phar install` or `composer install` in the directory of the composer.json file.

Usage
-----

```php
require __DIR__ . '/vendor/autoload.php';
require_once 'libs/GPG.php';

$gpg = new GPG();

// create an instance of a GPG public key object based on ASCII key
$pub_key = new GPG_Public_Key($public_key_ascii);

// using the key, encrypt your plain text using the public key
$encrypted = $gpg->encrypt($pub_key,$plain_text_string);

echo $encrypted;

```

License
-------

GPL http://www.gnu.org/copyleft/gpl.html

I'd like to release this under a more permissive license, but since PGP & GPG itself are GPL, I think this library is likely bound to the terms of GPL as well.
