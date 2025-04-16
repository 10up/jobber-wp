<?php
/**
 * Class responsible for encrypting and decrypting data.
 *
 * @package Jobber
 */

namespace Jobber;

/* phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_error_log */

/**
 * Class Enryption
 */
final class Encryption {

	/**
	 * Key to use for encryption.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * Salt to use for encryption.
	 *
	 * @var string
	 */
	private $salt;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->key  = $this->get_default_key();
		$this->salt = $this->get_default_salt();
	}

	/**
	 * Encrypt a string.
	 *
	 * @param string $value Value to encryption.
	 * @return string|bool Encrypted value. If encryption fails, returns the original value or false under certain conditions.
	 */
	public function encrypt( string $value ): string {
		try {
			$key   = sodium_crypto_generichash( $this->key, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
			$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		} catch ( \Exception $e ) {
			error_log( 'Jobber encryption failed.' );
			error_log( 'Exception: ' . $e->getMessage() );
			error_log( 'Trace: ' . $e->getTraceAsString() );

			// Return the original value if fail to generate nonce and key.
			return $value;
		}

		try {
			$encrypted = sodium_crypto_secretbox( $value . $this->salt, $nonce, $key );
			return sodium_bin2base64( $nonce . $encrypted, SODIUM_BASE64_VARIANT_ORIGINAL );
		} catch ( \Exception $e ) {
			error_log( 'Jobber encryption failed.' );
			error_log( 'Exception: ' . $e->getMessage() );
			error_log( 'Trace: ' . $e->getTraceAsString() );

			// Return false if encryption fails.
			return false;
		}
	}

	/**
	 * Decrypt a string.
	 *
	 * @param string $encrypted Encrypted value for decryption.
	 * @return string|false Decrypted value. If decryption fails, returns the original value or false under certain conditions.
	 */
	public function decrypt( string $encrypted ): string {
		try {
			$decoded = sodium_base642bin( $encrypted, SODIUM_BASE64_VARIANT_ORIGINAL );
			$key     = sodium_crypto_generichash( $this->key, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );

			$nonce            = mb_substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit' );
			$encrypted_result = mb_substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' );
		} catch ( \Exception $e ) {
			error_log( 'Jobber decryption failed.' );
			error_log( 'Exception: ' . $e->getMessage() );
			error_log( 'Trace: ' . $e->getTraceAsString() );

			// Return the original value if fail to get decoded value or nonce and key.
			return $encrypted;
		}

		try {
			$value = sodium_crypto_secretbox_open( $encrypted_result, $nonce, $key );
			if ( ! $value || substr( $value, - strlen( $this->salt ) ) !== $this->salt ) {
				error_log( 'Jobber decryption failed.' );
				return false;
			}
			return substr( $value, 0, - strlen( $this->salt ) );
		} catch ( \Exception $e ) {
			error_log( 'Jobber decryption failed.' );
			error_log( 'Exception: ' . $e->getMessage() );
			error_log( 'Trace: ' . $e->getTraceAsString() );

			return false;
		}
	}

	/**
	 * Gets the default encryption key to use.
	 *
	 * @return string Default (not user-based) encryption key.
	 */
	private function get_default_key() {
		if ( defined( 'JOBBER_ENCRYPTION_KEY' ) && '' !== JOBBER_ENCRYPTION_KEY ) {
			return JOBBER_ENCRYPTION_KEY;
		}

		if ( defined( 'LOGGED_IN_KEY' ) && '' !== LOGGED_IN_KEY ) {
			return LOGGED_IN_KEY;
		}

		// Ideally this default is never used but we have it just in case.
		return 'vJgwa_qf0u(k!uir[rB);g;DciNAKuX;+q&`A+z&m6kX3Y|$q.U3:Q>!$)6CA+=O';
	}

	/**
	 * Gets the default encryption salt to use.
	 *
	 * @return string Encryption salt.
	 */
	private function get_default_salt() {
		if ( defined( 'JOBBER_ENCRYPTION_SALT' ) && '' !== JOBBER_ENCRYPTION_SALT ) {
			return JOBBER_ENCRYPTION_SALT;
		}

		if ( defined( 'LOGGED_IN_SALT' ) && '' !== LOGGED_IN_SALT ) {
			return LOGGED_IN_SALT;
		}

		// Ideally this default is never used but we have it just in case.
		return '|qhC}/w6q+$V`H>wM:AtNpg/{s)g<k{F:WMcvJJD[K6c_Kb1OEy^Yx7f|$Ovm+X|';
	}
}
