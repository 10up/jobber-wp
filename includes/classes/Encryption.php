<?php
/**
 * Jobber Encyption Class
 *
 * @package Jobber
 */

namespace Jobber;

/**
 * Encryption Class.
 */
final class Encryption {

	/**
	 * Encyrption Cipher
	 * Default is AES-256-CBC.
	 *
	 * @var string
	 */
	private $cipher;

	/**
	 * Encryption Algorithm.
	 * Default is sha256.
	 *
	 * @var string
	 */
	private $algorithm;

	/**
	 * Options.
	 * Default is OPENSSL_RAW_DATA.
	 *
	 * @var int
	 */
	private $options;

	/**
	 * Construct the Encyrption Class.
	 *
	 * @param string $cipher    The cipher to use.
	 * @param string $algorithm The algorithm to use.
	 * @param int    $options   The option to use. Default is 1 (OPENSSL_RAW_DATA).
	 */
	public function __construct( $cipher = 'AES-256-CBC', $algorithm = 'sha256', $options = 1 ) {
		$this->cipher    = $cipher;
		$this->algorithm = $algorithm;
		$this->options   = $options;
	}

	/**
	 * Encrypt a string
	 *
	 * @param string $string The string to encrypt.
	 * @return string
	 */
	public function encrypt( $string ) {
		$iv     = openssl_random_pseudo_bytes( openssl_cipher_iv_length( $this->cipher ) );
		$cipher = openssl_encrypt( $string, $this->cipher, AUTH_KEY, $this->options, $iv );
		$hmac   = hash_hmac( $this->algorithm, $cipher, AUTH_SALT, true );

		return $iv . $hmac . $cipher;
	}

	/**
	 * Decrypt a string
	 *
	 * @param string $string The string to decrypt.
	 * @return string|false
	 */
	public function decrypt( $string ) {
		$iv_length = openssl_cipher_iv_length( $this->cipher );
		$iv        = substr( $string, 0, $iv_length );
		$hmac      = substr( $string, $iv_length, 32 );
		$cipher    = substr( $string, $iv_length + 32 );

		$decrypted       = openssl_decrypt( $cipher, $this->cipher, AUTH_KEY, $this->options, $iv );
		$calculated_hmac = hash_hmac( $this->algorithm, $cipher, AUTH_SALT, true );

		if ( hash_equals( $hmac, $calculated_hmac ) ) {
			return $decrypted;
		}

		return false;
	}
}

