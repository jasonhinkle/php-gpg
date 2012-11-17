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
class EncryptTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	function setUp()
	{
		echo "\n";
		echo "EncryptTest::setUp\n";
	}
	
	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	function tearDown()
	{
	}
	
	/**
	 * Test that basic encryption returns a valid encrypted message
	 */
	function test_Encrypt()
	{
		
		echo "EncryptTest::test_Encrypt\n";
		
		// jason's public key
		$public_key_ascii = "-----BEGIN PGP PUBLIC KEY BLOCK-----
Version: GnuPG/MacGPG2 v2.0.17 (Darwin)

mQGiBD9cmv0RBACopoo6H3Gz5eb0NWeATuvBObvzkKL6E4GvCH44lZosjnZJHJkL
Jrs7E0l7kg/e7PhjwMEEPHnPniqk6+UvB5C75VeEACYvhAk3T4pOsPUxNMpU/S0k
X1Y+c84H3yCxDoLTEzLeA48wGRaZFnV8jsZAJ8JWrvPT893pvLRWHublkwCg+PQj
MxGEFGwaytCly8XA6pn/0/sEAJp4bg4OKMn8FuxEIP0Y0w42bMeAmh45j34g0s5A
SsJVsm14BpGD2yDqpLuBGkoewtzxEHH3pAqNkP3virx1qUQXocZVVN7cfxIb8PYC
PX4LbREb4tkhBSJsk8AzamuUCmjnLjdX0XojfR6W5aj983g2RMET3ODdlPDPRqGs
PlRkBACAHytJ5j2Ts+6F4iJXcnIjpSh3UYXBzr8Iz6Z9qed2+c+zJrjMJ+P7tOqC
/4mX62arDUvZtYNaPVPFv7Vkq4nDy84vQPEcmo29IDHbjfvTX8a+JULx6w8Cz22H
oWjWWxfCXMKRKM9ENPyxsfoqqImTfA0V3jesddHFnvtaqvl8nrQoSmFzb24gKERl
dmVsb3BlcikgPGphc29uQHZlcnlzaW1wbGUuY29tPohZBBMRAgAZBQI/XJr9BAsH
AwIDFQIDAxYCAQIeAQIXgAAKCRDJM15+CzZ/Z/YmAJ9SC2GyWL0E6h15/Yblj2Vy
4uHg9gCgyLVoLgqeA3xDPySBnB68yN3Zb2iJARwEEwECAAYFAk5bEG0ACgkQu5nw
PoTrwLvaBgf/figc8K/xK87J1J9cug+kBVOfwAt7LuqnAgSvUftAsm9oU6dU9kMp
yzfSBwKYC10OvxwXjSdyVv8c+neN49UXuGL58XeKm1UgFi0DgN8CM079B/HUcYTO
d6E+lMpFyRZKfPck9FNRJ6fIKgRaMrr/ayQFkGvtqsC5SenGZEnrUf1Y+b3A5Fww
bEhS59Jx58JucLqUH1KWpOr6TR7mgjLvxxWDqvm+Q4i5q/miqRedIjC/C9rkUWyP
9NK6pjIXDJZzrLXzx1NJUaqaJaJBiRg160MFSMJvHNrIkJsV6izIwfItyH11hd4t
X07wTf1iPTpkykJMwXkJK3Cv/ZcT2C94OLkBDQQ/XJr/EAQA4NjC5rN7uGEJsvv+
vZ3jKMJUFacmLygz3VgPd2V1hUnmNFIo3PmucUiGtBCO5ZJJ7jyC+2y/RrFP1Imx
jSBeguFMgm9eMSjhRicFm1N1/PMcWKGrf4gcnCpIKvI5RTpsl0tNYy5bOByJYHWC
DX1HoH3AfmOTlCQkjLXfNARUpG8ABA0EAKBQBaotVlc+LcgxboRQHF4x0xkmMNGW
Wy21s3J8AsSiqhTpUg37M7YNvZcik/8+9oE1YOG1k1/qJsPsGqpbs+l/yNNZ3ggY
C3RMT9lGIiCB785xB0sR2VSzUfgwBWp9TBSHfOH/isQOoAUyhhQfR973qObfEbG6
hO7bkmvZiUnciEYEGBECAAYFAj9cmv8ACgkQyTNefgs2f2ewzgCfSb95uullA+jW
sXccoV2YG+nLs0wAoNCywmXTKSKMmKqyY7+7y8rV9Bwx
=kzqb
-----END PGP PUBLIC KEY BLOCK-----";
		
		// plain text message
		$plain_text_string = "This is a test message";
		
		$gpg = new GPG();
		$pub_key = new GPG_Public_Key($public_key_ascii);
		$encrypted = $gpg->encrypt($pub_key,$plain_text_string);
		
		$this->assertContains('-----BEGIN PGP MESSAGE-----', $encrypted, 'PGP Header Expected');
		
		$this->assertContains('-----END PGP MESSAGE-----', $encrypted, 'PGP Footer Expected');

	}

}

?>