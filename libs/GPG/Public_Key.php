<?php
/**
 * Original source: https://github.com/jasonhinkle/php-gpg
 * Might contain some changes by buli <phppgp@taurit.pl>
 */

require_once("Expanded_Key.php");

define("PK_TYPE_ELGAMAL", 1);
define("PK_TYPE_RSA", 0);
define("PK_TYPE_UNKNOWN", -1);

/**
 * Pure PHP implementation of PHP/GPG public key
 *
 * @package php-gpg::GPG
 * @link http://www.verysimple.com/
 * @copyright  1997-2011 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @todo implement decryption
 * @version 1.0
 */
class GPG_Public_Key {
    var $version;
	var $fp;
	var $key_id;
	var $user;
	var $public_key;
	var $type;
	
	/**
	 * Returns true if the key was successfuly parsed from the ASCII-armored data.
	 * @return mixed
	 */
	function IsValid()
	{
		return $this->version != -1 && $this->GetKeyType() != PK_TYPE_UNKNOWN;
	}
	
	/**
	 * Returns algorithm name present in the key.
	 * @return mixed PK_TYPE_ELGAMAL, PK_TYPE_RSA or PK_TYPE_UNKNOWN
	 */
	function GetKeyType()
	{
		if (!strcmp($this->type, "ELGAMAL")) return PK_TYPE_ELGAMAL;
		if (!strcmp($this->type, "RSA")) return PK_TYPE_RSA;
		return PK_TYPE_UNKNOWN;
	}

	/**
	 * Returns computed fingerprint for the last found public key or subkey in the data
	 * Note: In the case of a key with sub-keys, his method will always return the 
	 * fingerprint for the key that is used for encryption, which may not 
	 * necessarily match the identifying fingerprint that is displayed by other clients
	 * @return mixed
	 */
	function GetFingerprint()
	{
		return strtoupper( trim(chunk_split($this->fp, 4, ' ')) );
	}
	
	/**
     * Returns the 16-bytes ID of the last found public key or subkey in the data
	 * Note: In the case of a key with sub-keys, his method will always return the 
	 * ID for the key that is used for encryption, which may not 
	 * necessarily match the identifying ID that is displayed by other clients
	 * @return mixed
	 */
	function GetKeyId()
	{
		return (strlen($this->key_id) == 16) ? strtoupper($this->key_id) : '0000000000000000';
	}
	
	/**
     * Returns public key part of the the last found public key or subkey in the data
	 * @return mixed
	 */
	function GetPublicKey()
	{
		return str_replace("\n", "", $this->public_key);
	}
	
	/**
     *  Constructor. Parse public key and expose as an object model properties.
     *  Actually, since the 'public key' is a collection of keys, only the last key in the data block will be chosen as an encryption key and returned in this object.
     *  
     *  See e.g. https://www.void.gr/kargig/blog/2013/12/02/creating-a-new-gpg-key-with-subkeys/
     *  for some explanation why there might be more than 1 key in a "public key"
	 * @param mixed $asc ASCII-Armored public PGP key data
	 * @throws Exception if the key data is invalid or contains functions that are not supported
	 */
	function GPG_Public_Key($asc) {
		$found = 0;
		
		$asc = str_replace("\r\n", "\n", $asc); // normalize line breaks
		
		if (strpos($asc, "-----BEGIN PGP PUBLIC KEY BLOCK-----\n") === false)
			throw new Exception("Missing header block in Public Key");

		if (strpos($asc, "\n\n") === false)
			throw new Exception("Missing body delimiter in Public Key");
		
		if (strpos($asc, "\n-----END PGP PUBLIC KEY BLOCK-----") === false)
			throw new Exception("Missing footer block in Public Key");
		
		// get rid of everything except the base64 encoded key
		$headerbody = explode("\n\n", str_replace("\n-----END PGP PUBLIC KEY BLOCK-----", "", $asc), 2);
		$asc = trim($headerbody[1]);
		// now $asc is a raw PGP data, without headers and starter/footer block
		
		$len = 0;
		$s =  base64_decode($asc); // decode from Radix-64 to array of octets
		$sa = str_split($s); // convert string to array of elements
		
		for($i = 0; $i < strlen($s);) { // iterate through all octets
            
            // The first octet of the packet header is called the "Packet Tag".  It
            // determines the format of the header and denotes the packet contents.
            // The remainder of the packet header is the length of the packet.
            // This is described in https://tools.ietf.org/html/rfc4880#section-4.2
			$tag = ord($sa[$i++]);
			if(($tag & 128) == 0) break; // Bit 7 -- Always one or it is not a valid  packet header octet
            
			if($tag & 64) {
                // bit #7 is on: new packet format
                // this is recommended packet format, but we're not in the control, we're just reading the key...
                
                // New format packets contain:
                // Bits 5-0 -- packet tag
				$tag &= 63; // make bit#6 and #7 equal to zero. the rest of bits gives us a packet tag
                
                // Now following the https://tools.ietf.org/html/rfc4880#section-4.2.2
				$len = ord($sa[$i++]); // get first octet containing length
                
                //if ($tag == 6 || $tag == 14) { echo "tag&64 len = $len\n"; } // problem: when length = 192 it gets transformed to 72 and it should be > 192
                
				if ($len > 191 && $len < 224)
                    $len = (($len - 192) << 8) + ord($sa[$i++]) + 192; // there was a bug here - fixed with RFC4880 4.2.2.2.
				else if ($len == 255)
                    $len = (ord($sa[$i++]) << 24) + (ord($sa[$i++]) << 16) + (ord($sa[$i++]) << 8) + ord($sa[$i++]);
                else if ($len > 223 && $len < 255)
                    $len = (1 << ($len & 0x1f));
                // else, length is less or equal 191, so no transform is needed.
			} else {
                // bit #7 is off: old packet format
                
                // Old format packets contain:
                // Bits 5-2 -- packet tag
                // Bits 1-0 -- length-type
				$len = $tag & 3; // leave only last 2 bits as length type
                
                /* Length type is interpreted this way:                  
                 *  0 - The packet has a one-octet length.  The header is 2 octets long.
                 *  1 - The packet has a two-octet length.  The header is 3 octets long.
                 *  2 - The packet has a four-octet length.  The header is 5 octets long.
                 *  3 - The packet is of indeterminate length. The header is 1 octet long, and the implementation must determine how long the packet is.
                 */
                
				$tag = ($tag >> 2) & 15;
				if ($len == 0)
                    $len = ord($sa[$i++]);
				else if($len == 1)
                    $len = (ord($sa[$i++]) << 8) + ord($sa[$i++]);
                else if($len == 2)
                    $len = (ord($sa[$i++]) << 24) + (ord($sa[$i++]) << 16) + (ord($sa[$i++]) << 8) + ord($sa[$i++]);
                else
                    $len = strlen($s) - 1;
			}
            
            /*  5.5.1.1.  Public-Key Packet (Tag 6)

            A Public-Key packet starts a series of packets that forms an OpenPGP
            key (sometimes called an OpenPGP certificate).

            5.5.1.2.  Public-Subkey Packet (Tag 14)

            A Public-Subkey packet (tag 14) has exactly the same format as a
            Public-Key packet, but denotes a subkey.  One or more subkeys may be
            associated with a top-level key.  By convention, the top-level key
            provides signature services, and the subkeys provide encryption
            services.

            Note: in PGP 2.6.x, tag 14 was intended to indicate a comment
            packet.  This tag was selected for reuse because no previous version
            of PGP ever emitted comment packets but they did properly ignore*/
            
            // determine (by checking tag number) if the packet is either:
            // 6        -- Public-Key Packet
            // 14       -- Public-Subkey Packet
			if ($tag == 6 || $tag == 14) { 
				$k = $i;
				$version = ord($sa[$i++]); // next octet contains version (as of 2014-10-04, version 4 is required for new implementations when generating key)
				$found = 1; // public key was found in the data block
				$this->version = $version;
				
                // the next 4 octets contain the time key was created
				$time = (ord($sa[$i++]) << 24) + (ord($sa[$i++]) << 16) + (ord($sa[$i++]) << 8) + ord($sa[$i++]);
				
                // Old versions of standard contain validity period in next 2 octets:
                //  - A two-octet number denoting the time in days that this key is valid.  If this number is zero, then it does not expire.
				if($version == 2 || $version == 3) $valid = ord($sa[$i++]) << 8 + ord($sa[$i++]);
				
                // The version 4 format is similar to the version 3 format except for
                // the absence of a validity period.

                //- A one-octet number denoting the public-key algorithm of this key.
                /* 1          - RSA (Encrypt or Sign) [HAC]
                2          - RSA Encrypt-Only [HAC]
                3          - RSA Sign-Only [HAC]
                16         - Elgamal (Encrypt-Only) [ELGAMAL] [HAC]
                17         - DSA (Digital Signature Algorithm) [FIPS186] [HAC]
                18         - Reserved for Elliptic Curve
                19         - Reserved for ECDSA
                20         - Reserved (formerly Elgamal Encrypt or Sign)
                21         - Reserved for Diffie-Hellman (X9.42, as defined for IETF-S/MIME)
                 */
				$algo = ord($sa[$i++]);
				
				if($algo == 1 || $algo == 2) {
                    // RSA key that can be used for encryption is present in the public key.
                    $this->type = "RSA";
                    
                    // This algorithm contains two MultiPrecision Integers (MPI's) in algorithm-specific section of the data, that need to be parsed now.
                    // MPIs contain two octets containing length of an integer, and more octets with actual data. All big-endian.
                    // MPI specification: https://tools.ietf.org/html/rfc4880#page-9
                    
					$startPosition = $i; // save start position for the RSA's algorithm-specific data
					$lm = floor((ord($sa[$i]) * 256 + ord($sa[$i + 1]) + 7) / 8); // length of integer containing public modulus n
					$mod = substr($s, $startPosition, $lm + 2); // whole MPI for public modulus n
                    
                    $i += $lm + 2; // move the pointer position to the start of actual data for public modulus n
					$le = floor((ord($sa[$i]) * 256 + ord($sa[$i+1]) + 7) / 8); // length of integer containing public exponent e
					$this->public_key = base64_encode(substr($s, $startPosition, $lm + $le + 4)); // store two MPIs for n and e, in Radix-64
                    $i += $le + 2; // move the pointer to the start of e data
                    
					if ($version == 3) {
						$this->fp = '';
                        
                        // For a V3 key, the eight-octet Key ID consists of the low 64 bits of the public modulus of the RSA key.
						$this->key_id = bin2hex(substr($mod, strlen($mod) - 8, 8));
					} else if($version == 4) {
						// https://tools.ietf.org/html/rfc4880#section-12
                        /* A V4 fingerprint is the 160-bit SHA-1 hash of the octet 0x99,
                        followed by the two-octet packet length, followed by the entire
                        Public-Key packet starting with the version field.  The Key ID is the
                        low-order 64 bits of the fingerprint. Example:
                        a.1) 0x99 (1 octet)
                        a.2) high-order length octet of (b)-(e) (1 octet)
                        a.3) low-order length octet of (b)-(e) (1 octet)
                        b) version number = 4 (1 octet);
                        c) timestamp of key creation (4 octets);
                        d) algorithm (1 octet): 17 = DSA (example);
                        e) Algorithm-specific fields.
                         */
                        
                        // currently commited implementation
					    // $headerPos = strpos($s, chr(0x04));  // TODO: is this always the correct starting point for the pulic key packet 'version' field?
						// $delim = chr(0x01) . chr(0x00);  // TODO: is this the correct delimiter for the end of the public key packet? 
						// $delimPos = strpos($s, $delim) + (3-$headerPos);
						// $pkt = chr(0x99) . chr($delimPos >> 8) . chr($delimPos & 255) . substr($s, $headerPos, $delimPos);
                        //echo "current-delimpos: ".($delimPos )."\n";
                        // the code above that was used previously seems wrong as it computes the fingerprint for the first key in the data block (which is usually main key), while the code 
                        // below (that is currently used) computes fingerprint for the key currently processed
						//echo "\npktcurrent: $pkt\n";
                        
                        // old implementation:
						// this does not work, tried it with RSA 1024 and RSA 4096 keys generated by GnuPG v2 (2.0.29) on Windows running Apache and PHP 5.6.3
						// $pkt = chr(0x99) . chr($delimPos >> 8) . chr($delimPos & 255) . substr($s, $headerPos, $delimPos);

						// this is the original signing string which seems to have only worked for key lengths of 1024 or less
						$pkt = chr(0x99) . chr($len >> 8) . chr($len & 255) . substr($s, $k, $len); // use this for now
                        //echo "old-len: ".($len )."\n";
                        //echo "\npktold: $pkt\n";
                        // why does chr($len >> 8) give x84 in js-generated key?
						
                        
						$fp = sha1($pkt);
						$this->fp = $fp;
						$this->key_id = substr($fp, strlen($fp) - 16, 16);
                        //echo "PHP-PGP: computed key_id = ".$this->key_id." - tag = $tag\n";
				
					} else {
						throw new Exception('GPG Key Version ' . $version . ' is not supported');
					}
					$found = 2;
				} else if(($algo == 16 || $algo == 20) && $version == 4) {
                    // Public key contains ElGamal algorithm that can be used for encryption
                    $startPosition = $i;
                    
                    $lp = floor((ord($sa[$i]) * 256 + ord($sa[$i +1]) + 7) / 8);
                    $i += $lp + 2;
                    
                    $lg = floor((ord($sa[$i]) * 256 + ord($sa[$i + 1]) + 7) / 8);
                    $i += $lg + 2;
                    
                    $ly = floor((ord($sa[$i]) * 256 + ord($sa[$i + 1]) + 7)/8);
                    $i += $ly + 2;
                    
                    $this->public_key = base64_encode(substr($s, $startPosition, $lp + $lg + $ly + 6));
                    
                    // TODO: should this be adjusted as it was for RSA (above)..? // not tested yet, but the adjustment above doesn't seem right
                    $pkt = chr(0x99) . chr($len >> 8) . chr($len & 255) . substr($s, $k, $len);
                    $fp = sha1($pkt);
                    $this->fp = $fp;
                    $this->key_id = substr($fp, strlen($fp) - 16, 16);
                    $this->type = "ELGAMAL";
                    $found = 3;
                } else {
                    $i = $k + $len;
                }
			} else if ($tag == 13) {
                // this is user id packet
                $this->user = substr($s, $i, $len); // should be compliant with https://www.ietf.org/rfc/rfc2822.txt section 3.4
				$i += $len;
            } else {
                $i += $len;
            }
        }
		
		if($found < 2) {  
			// no public key or subkey (with tag equal 6 or 14) was found among the data blocks
			throw new Exception("Unable to parse Public Key");
            // 			$this->version = "";
            // 			$this->fp = "";
            // 			$this->key_id = "";
            // 			$this->user = ""; 
            // 			$this->public_key = "";
		}
	}
	
	function GetExpandedKey()
	{
		$ek = new Expanded_Key($this->public_key);
	}
}

?>
