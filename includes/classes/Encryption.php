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
		$iv_len = openssl_cipher_iv_length( $this->cipher );
		$iv     = openssl_random_pseudo_bytes( $iv_len );

		$cipher_text = openssl_encrypt( $string, $this->cipher, AUTH_KEY, $this->options, $iv );
		$hmac        = hash_hmac( $this->algorithm, $cipher_text, AUTH_SALT, true );
		return "{$hmac}{$iv}{$cipher_text}";
	}

	/**
	 * Decrypt a string
	 *
	 * @param string $string The string to decrypt.
	 * @return string|false
	 */
	public function decrypt( $string ) {
		$sha2_len = 32;
		$iv_len   = openssl_cipher_iv_length( $this->cipher );
		$iv       = substr( $string, 0, $iv_len );
		$hmac     = substr( $string, $iv_len, $sha2_len );
		$cipher   = substr( $string, $iv_len + $sha2_len );
		$original = openssl_decrypt( $cipher, $this->cipher, AUTH_KEY, $this->options, $iv );
		$calcmac  = hash_hmac( $this->algorithm, $cipher, AUTH_SALT, true );

		return hash_equals( $hmac, $calcmac ) ? $original : false;
	}
}

