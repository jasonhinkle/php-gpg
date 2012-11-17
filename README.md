php-gpg
=======

php-gpg is a pure PHP implementation of PHP/GPG encryption.  

Features/Limitations
--------------------

 * Supports RSA, DSA public key length of 2,4,8,16,512,1024,2048 or 4096
 * Currently supports only encrypt
 
Usage
-----

```php
require_once 'libs/GPG.php';

$gpg = new GPG();

// create an instance of a GPG public key object based on ASCII key
$pub_key = new GPG_Public_Key($public_key_ascii);

// using the key, encrypt your plain text using the public key
$encrypted = $gpg->encrypt($pub_key,$plain_text_string);

echo $encrypted;

```