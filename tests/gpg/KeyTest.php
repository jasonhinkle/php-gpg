<?php
/**
 * @package GPG::Tests
 */
/* ensure the framework libraries can be located */
set_include_path(
		realpath("../libs") .
		PATH_SEPARATOR . get_include_path()
);

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'GPG.php';

/**
 * 
 */
class KeyTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	function setUp()
	{
	}
	
	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	function tearDown()
	{
	}
	
	function getGnuPGDSAKey()
	{
		return "-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG/MacGPG2 v2.0.22 (Darwin)
Comment: GPGTools - https://gpgtools.org

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
fKa+Elh9Jb2ioS9LQ+TtUZnJQWHnlXVg
=nOdq
-----END PGP PUBLIC KEY BLOCK-----";
	}
	
	/**
	 * Return a public key used for encryption
	 * @return string PGP public key
	 */
	function getOpenPGPTestKey1()
	{
		return "-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: OpenPGP.js v.1.20140106
Comment: http://openpgpjs.org

xo0EUsvlVAEEAJN0OBFbVM21z3OzG9DDCohpaF9+rWUyCreRIDC+Cou9680f
t8v5m9B0+BTM2OVEB3uK7QbcWhsPkfpn7zW9YntStSXI5ESC+eAhNGbAKnvq
32MadP7DbwRUrtcldSEd2B6uI/embnmv1CBkmrTAh0MiKscHPh5xrxwcxLLJ
TQstABEBAAHNF1Rlc3QgPHRlc3RAZXhhbXBsZS5jb20+wpwEEAEIABAFAlLL
5VUJEI3OSY9gkd/WAAAgKwP/TUeVmXMS3YYvTLC0tRw/Ae1M4sNduvuKcza2
4l6iuiIhXHntL9u5pQERcyhzL171DsR1S3EUj/nIFpfqN8OWOuj0Wqt3H4F9
igkfsrJr68qHEQztSXRufP/jv7OX+0EF+ve0+IFa6r5LPRmibl0qta522gHd
2LVudfBDDi0Y6k4=
=JjPg
-----END PGP PUBLIC KEY BLOCK-----";
	}
	
	/**
	 * Return a public key used for encryption
	 * @return string PGP public key
	 */
	function getOpenPGPTestKey2()
	{
		return "-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: OpenPGP.js v.1.20140106
Comment: http://openpgpjs.org

xsBNBFLL5JEBCACFBaNfA4Iicm/MvO0sJcllB/iGc0qNgtqwpXGBydDt8Pj7
+mn0ZZ4Q1ol5wAYAxapGGaoEB1Wh5ALy2UvnrFx1/dyZEQhU3oUTRQevzhm1
/gp/0l9bNijBONir1spMuDc7okXWaR8GCW0mWwmeFgXZnjL4Dmr6dUJvZHpX
ZiekLPT3xHWQxrKdsafxmULVgd71ZdBq/6ikWQsK5XqBVykD2C/6jNDJ8Cga
wzCs8WOSQkpss7L28qQrEkL3+JaLGEe3+6UUnzs5KjwgTZvlAawDkm55corz
TistbvOotDVbs/nXyu6TnMhZXqJR2a0TEuRDXpS93qGr97flZKwVBp6vABEB
AAHNF1Rlc3QgPHRlc3RAZXhhbXBsZS5jb20+wsBcBBABCAAQBQJSy+SZCRDI
dThpeYYhmgAAVecH/R2IXW82smV41eDIwA9PzV6sKF7ywmlEl/zkZTEpTLaB
bXM3wScspVHGWV7hO+9ZQImtdcqb2AFpNpBgBtpFypM2nMGdFxTzv0m3uRUs
J8KT0lU0Uj4FEsr5YYCb1UWGgDrZP+vZFxfw8j+NWGIS5NlLHPeTTMJVRhKX
Xe0c8AaAF8qz10+JyapyDlUlFVr8+n/UjGwsUp15ODnDt0nsGTfw6jKelTU2
y/d0ckERWE0rWT2eooAxqxIoVayJkEZTzbfx/mHJXeGhOFwjg5danv7JnDI/
FHAUVqBD8zTkqmjdk9LZ6GnF6tuKRAWS59qU7y4+fchP58dhjep0mIFa6FQ=
=8aNP
-----END PGP PUBLIC KEY BLOCK-----";
	}
	
	/**
	 * Return a public key used for encryption
	 * @return string PGP public key
	 */
	function getGnuPGTestKey()
	{
		return "-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG/MacGPG2 v2.0.22 (Darwin)
Comment: GPGTools - https://gpgtools.org

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
0w==
=Opd1
-----END PGP PUBLIC KEY BLOCK-----";
	}
	
	/**
	 * Test key ID
	 */
	function test_VerifyGnuPGKey()
	{
		// jason's public key
		$public_key_ascii = $this->getGnuPGTestKey();
	
		$gpg = new GPG();
		$pub_key = new GPG_Public_Key($public_key_ascii);
	
		$this->assertEquals(PK_TYPE_RSA,$pub_key->GetKeyType(),'OpenPGP Incorrect Key Type');
		// $this->assertEquals('47009B66424E9476',$pub_key->GetKeyId(),'OpenPGP Incorrect Key ID');
		// $this->assertEquals('ED4F E89E 38A3 7833 3CD4 D6FA 4700 9B66 424E 9476',$pub_key->GetFingerprint(),'OpenPGP Incorrect Fingerprint');
		$this->assertEquals('04343C988F215A2E',$pub_key->GetKeyId(),'OpenPGP Incorrect Key ID');
		$this->assertEquals('DA76 9989 C01E 4B6F 9E44 B19A 0434 3C98 8F21 5A2E',$pub_key->GetFingerprint(),'OpenPGP Incorrect Fingerprint');
		
	}

	/**
	 * Test key ID
	 */
	function test_VerifyOpenPGPKey1()
	{
		// OpenPGP Test Key
		$public_key_ascii = $this->getOpenPGPTestKey1();
	
		$gpg = new GPG();
		$pub_key = new GPG_Public_Key($public_key_ascii);
			
		$this->assertEquals(PK_TYPE_RSA,$pub_key->GetKeyType(),'OpenPGP Incorrect Key Type');
		$this->assertEquals('8DCE498F6091DFD6',$pub_key->GetKeyId(),'OpenPGP Incorrect Key ID');
		$this->assertEquals('C893 35AC EDF1 6046 7534 B25E 8DCE 498F 6091 DFD6',$pub_key->GetFingerprint(),'OpenPGP Incorrect Fingerprint');
	
	}
	
	/**
	 * Test key ID
	 */
	function test_VerifyOpenPGPKey2()
	{
		// OpenPGP Test Key
		$public_key_ascii = $this->getOpenPGPTestKey2();

		$gpg = new GPG();
		$pub_key = new GPG_Public_Key($public_key_ascii);
			
		$this->assertEquals(PK_TYPE_RSA,$pub_key->GetKeyType(),'OpenPGP Incorrect Key Type');
		$this->assertEquals('C87538697986219A',$pub_key->GetKeyId(),'OpenPGP Incorrect Key ID');
		$this->assertEquals('3C05 9D07 C624 84A4 EF2D 3651 C875 3869 7986 219A',$pub_key->GetFingerprint(),'OpenPGP Incorrect Fingerprint');
	
	}
	
	function test_VerifyGnuPGDSAKey()
	{
		// OpenPGP Test Key
		$public_key_ascii = $this->getGnuPGDSAKey();
		
		$gpg = new GPG();
		$pub_key = new GPG_Public_Key($public_key_ascii);
			
		$this->assertEquals(PK_TYPE_ELGAMAL,$pub_key->GetKeyType(),'OpenPGP Incorrect Key Type');
		// $this->assertEquals('4C3597FC3DBAA630',$pub_key->GetKeyId(),'OpenPGP Incorrect Key ID');
		// $this->assertEquals('230B 39E5 984B 864E 94A4 B224 4C35 97FC 3DBA A630',$pub_key->GetFingerprint(),'OpenPGP Incorrect Fingerprint');
		$this->assertEquals('DF481A87E2905694',$pub_key->GetKeyId(),'OpenPGP Incorrect Key ID');
		$this->assertEquals('4A17 737B A6FC 6E1A 1AB3 8FD3 DF48 1A87 E290 5694',$pub_key->GetFingerprint(),'OpenPGP Incorrect Fingerprint');
		
		
	}

}

?>