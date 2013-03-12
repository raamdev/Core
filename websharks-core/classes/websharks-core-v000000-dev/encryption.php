<?php
/**
 * Encryption.
 *
 * Copyright: © 2012 (coded in the USA)
 * {@link http://www.websharks-inc.com WebSharks™}
 *
 * @author JasWSInc
 * @package WebSharks\Core
 * @since 120318
 */
namespace websharks_core_v000000_dev
{
	if(!defined('WPINC'))
		exit('Do NOT access this file directly: '.basename(__FILE__));

	/**
	 * Encryption.
	 *
	 * @package WebSharks\Core
	 * @since 120318
	 *
	 * @assert ($GLOBALS[__NAMESPACE__])
	 */
	class encryption extends framework
	{
		/**
		 * Determines the proper encryption/decryption key to use.
		 *
		 * @param string $key Optional. Attempt to force a specific key?
		 *
		 * @return string Proper encryption/decryption key.
		 *
		 * @throws exception If invalid types are passed through arguments lists.
		 * @throws exception If unable to obtain a valid encryption key, by any means.
		 *
		 * @assert ('key') === 'key'
		 * @assert () !empty TRUE
		 */
		public function key($key = '')
		{
			$this->check_arg_types('string', func_get_args());

			if(strlen($key)) return $key;

			$key = $this->©options->get('encryption.key');
			$key = (!strlen($key)) ? wp_salt() : $key;
			$key = (!strlen($key)) ? md5($this->©url->current_host()) : $key;

			if(!strlen($key))
				throw $this->©exception(
					__METHOD__.'#key_missing', NULL, $this->i18n('No encryption key.')
				);
			return $key; // It's a good day in Eureka!
		}

		/**
		 * Generates a unique encryption key.
		 *
		 * @param integer $length Optional. A specific key length.
		 *    Keys are signed with ``$this->hmac_sha256_sign()`` (so it's `64` characters in length, by default).
		 *    If a specific ``$length`` is needed, we cut the key short, or add additional random chars.
		 *
		 * @return string A keyed SHA256 hash of a unique access key.
		 *    The return value is `64` characters in length (by default).
		 */
		public function keygen($length = 64)
		{
			$this->check_arg_types('integer:!empty', func_get_args());
			$length = abs($length); // Force absolute value here.

			$prefix = $this->©url->current_host();
			$key    = $this->hmac_sha256_sign($this->©string->unique_id($prefix));

			if($length === 64) return $key; // The default length (64 chars).

			else if($length < 64) return (string)substr($key, 0, $length);

			return $key.$this->©string->random($length - 64, FALSE);
		}

		/**
		 * RIJNDAEL 256: two-way encryption/decryption, with a URL-safe base64 wrapper.
		 *
		 * @note This falls back on XOR encryption/decryption when/if mcrypt is not possible.
		 *
		 * @note Usually, it's better to use these ``encrypt()`` / ``decrypt()`` functions instead of XOR encryption;
		 *    because RIJNDAEL 256 offers MUCH better security. However, ``xencrypt()`` / ``xdecrypt()`` offer true consistency,
		 *    making them a better choice in certain scenarios. That is, XOR encrypted strings always offer the same representation
		 *    of the original string; whereas RIJNDAEL 256 changes randomly, making it difficult to use comparison algorithms.
		 *
		 * @param string  $string A string of data to encrypt.
		 *
		 * @param string  $key Optional. Key used for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @param boolean $w_md5_cs Optional. Defaults to TRUE (recommended).
		 *    When TRUE, an MD5 checksum is used in the encrypted string.
		 *
		 * @return string Encrypted string.
		 *
		 * @throws exception If invalid types are passed through arguments lists.
		 * @throws exception If string encryption fails.
		 *
		 * @assert ('hello') !empty TRUE
		 */
		public function encrypt($string, $key = '', $w_md5_cs = TRUE)
		{
			$this->check_arg_types('string', 'string', 'boolean', func_get_args());

			if($this->©function->is_possible('mcrypt_encrypt')
			   && in_array('rijndael-256', mcrypt_list_algorithms(), TRUE)
			   && in_array('cbc', mcrypt_list_modes(), TRUE)
			) // RIJNDAEL 256 encryption is possible?
			{
				if(!strlen($string)) // Nothing to encrypt?
					return ''; // Return now. Nothing more to do here.

				$key = (string)substr($this->key($key), 0, mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));
				$iv  = $this->©string->random(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), FALSE);

				$string = '~r2|'.$string;

				if(is_string($e = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $iv)) && strlen($e))
					$e = '~r2:'.$iv.(($w_md5_cs) ? ':'.md5($e) : '').'|'.$e;

				else throw $this->©exception(
					__METHOD__.'#failure', compact('e'),
					$this->i18n('String encryption failed (`e` NOT string).')
				);

				return ($base64 = $this->©string->base64_url_safe_encode($e));
			}
			else return $this->xencrypt($string, $key, $w_md5_cs);
		}

		/**
		 * RIJNDAEL 256: two-way encryption/decryption, with a URL-safe base64 wrapper.
		 *
		 * @note This falls back on XOR encryption/decryption when/if mcrypt is not possible.
		 *
		 * @note Usually, it's better to use these ``encrypt()`` / ``decrypt()`` functions instead of XOR encryption;
		 *    because RIJNDAEL 256 offers MUCH better security. However, ``xencrypt()`` / ``xdecrypt()`` offer true consistency,
		 *    making them a better choice in certain scenarios. That is, XOR encrypted strings always offer the same representation
		 *    of the original string; whereas RIJNDAEL 256 changes randomly, making it difficult to use comparison algorithms.
		 *
		 * @param string $base64 A string of data to decrypt.
		 *    Should still be base64 encoded.
		 *
		 * @param string $key Optional. Key used originally for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @return string Decrypted string, or an empty string if validation fails.
		 *    Validation may fail due to an invalid checksum, or a missing component in the encrypted string.
		 *    For security purposes, this returns an empty string on validation failures.
		 *
		 * @throws exception If invalid types are passed through arguments lists.
		 * @throws exception If a validated RIJNDAEL 256 string decryption fails.
		 *
		 * @assert $e = $this->object->encrypt('hello');
		 *    ($e) === 'hello'
		 *
		 * @assert $e = $this->object->encrypt('hello+there');
		 *    ($e) === 'hello+there'
		 *
		 * @assert $e = $this->object->encrypt('hello+there', 'caldwell.jason.key');
		 *    ($e, 'caldwell.jason.key') === 'hello+there'
		 *
		 * @assert $e = $this->object->encrypt('hello+there', 'caldwell.jason.key');
		 *    ($e, 'caldwell.jason.different.key') === ''
		 *
		 * @assert $e = $this->object->encrypt('hello+there', 'caldwell.jason.key');
		 *    ('hello', 'caldwell.jason.different.key') === ''
		 *
		 * @assert $e = $this->object->encrypt('hello+there', 'caldwell.jason.key');
		 *    ('hello', 'caldwell.jason.key') === ''
		 */
		public function decrypt($base64, $key = '')
		{
			$this->check_arg_types('string', 'string', func_get_args());

			if($this->©function->is_possible('mcrypt_decrypt')
			   && in_array('rijndael-256', mcrypt_list_algorithms(), TRUE)
			   && in_array('cbc', mcrypt_list_modes(), TRUE)
			   && strlen($e = $this->©string->base64_url_safe_decode($base64))
			   && preg_match('/^~r2\:(?P<iv>[a-zA-Z0-9]+)(?:\:(?P<md5>[a-zA-Z0-9]+))?\|(?P<e>.*?)$/s', $e, $iv_md5_e)
			) // RIJNDAEL 256 decryption is possible (and this IS an RIJNDAEL 256 encrypted string)?
			{
				$key = (string)substr($this->key($key), 0, mcrypt_get_key_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));

				if(strlen($iv_md5_e['e']) && (empty($iv_md5_e['md5']) || $iv_md5_e['md5'] === md5($iv_md5_e['e'])))
				{
					if(!is_string($string = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $iv_md5_e['e'], MCRYPT_MODE_CBC, $iv_md5_e['iv'])) || !strlen($string))
						throw $this->©exception(
							__METHOD__.'#failure', compact('string'),
							$this->i18n('String decryption failed (`string` NOT string).')
						);
				}
				else return ($string = ''); // Empty string if validation fails.

				if(strlen($string = preg_replace('/^~r2\|/', '', $string, 1, $r2)) && $r2)
					return ($string = rtrim($string, "\0\4" /* Right-trim NULLS and EOTs. */));
				else return ($string = ''); // Empty string if decryption fails validation.
			}
			else return $this->xdecrypt($base64, $key);
		}

		/**
		 * XOR two-way encryption/decryption, with a base64 wrapper.
		 *
		 * @note Usually, it's better to use the ``encrypt()`` / ``decrypt()`` functions instead of XOR encryption;
		 *    because RIJNDAEL 256 offers MUCH better security. However, ``xencrypt()`` / ``xdecrypt()`` offer true consistency,
		 *    making them a better choice in certain scenarios. That is, XOR encrypted strings always offer the same representation
		 *    of the original string; whereas RIJNDAEL 256 changes randomly, making it difficult to use comparison algorithms.
		 *
		 * @param string  $string A string of data to encrypt.
		 *
		 * @param string  $key Optional. Key used for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @param boolean $w_md5_cs Optional. Defaults to TRUE (recommended).
		 *    When TRUE, an MD5 checksum is used in the encrypted string.
		 *
		 * @return string Encrypted string.
		 *
		 * @throws exception If invalid types are passed through arguments lists.
		 * @throws exception If string encryption fails.
		 *
		 * @assert ('hello') !empty TRUE
		 */
		public function xencrypt($string, $key = '', $w_md5_cs = TRUE)
		{
			$this->check_arg_types('string', 'string', 'boolean', func_get_args());

			if(!strlen($string)) // Nothing to encrypt?
				return ''; // Return now. Nothing more to do here.

			for($key = $this->key($key), $string = '~xe|'.$string, $_i = 1, $e = ''; $_i <= strlen($string); $_i++)
			{
				$_char     = (string)substr($string, $_i - 1, 1);
				$_key_char = (string)substr($key, ($_i % strlen($key)) - 1, 1);
				$e .= chr(ord($_char) + ord($_key_char));
			}
			unset($_i, $_char, $_key_char);

			if(!strlen($e))
				throw $this->©exception(
					__METHOD__.'#failure', compact('e'),
					$this->i18n('String encryption failed (`e` is empty).')
				);

			$e = '~xe'.(($w_md5_cs) ? ':'.md5($e) : '').'|'.$e;

			return ($base64 = $this->©string->base64_url_safe_encode($e));
		}

		/**
		 * XOR two-way encryption/decryption, with a base64 wrapper.
		 *
		 * @note Usually, it's better to use the ``encrypt()`` / ``decrypt()`` functions instead of XOR encryption;
		 *    because RIJNDAEL 256 offers MUCH better security. However, ``xencrypt()`` / ``xdecrypt()`` offer true consistency,
		 *    making them a better choice in certain scenarios. That is, XOR encrypted strings always offer the same representation
		 *    of the original string; whereas RIJNDAEL 256 changes randomly, making it difficult to use comparison algorithms.
		 *
		 * @param string $base64 A string of data to decrypt.
		 *    Should still be base64 encoded.
		 *
		 * @param string $key Optional. Key used originally for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @return string Decrypted string, or an empty string if validation fails.
		 *    Validation may fail due to an invalid checksum, or a missing component in the encrypted string.
		 *    For security purposes, this returns an empty string on validation failures.
		 *
		 * @throws exception If invalid types are passed through arguments lists.
		 * @throws exception If a validated XOR string decryption fails.
		 *
		 * @assert $e = $this->object->xencrypt('hello');
		 *    ($e) === 'hello'
		 *
		 * @assert $e = $this->object->xencrypt('hello+there');
		 *    ($e) === 'hello+there'
		 *
		 * @assert $e = $this->object->xencrypt('hello+there', 'caldwell.jason.key');
		 *    ($e, 'caldwell.jason.key') === 'hello+there'
		 *
		 * @assert $e = $this->object->xencrypt('hello+there', 'caldwell.jason.key');
		 * // WARNING, this still decrypts, because the key is close enough, for the length of 'hello+there'.
		 *    ($e, 'caldwell.jason.different.key') === 'hello+there'
		 *
		 * @assert $e = $this->object->xencrypt('hello+there', 'caldwell.jason.key');
		 * // This works as expected, the key is different enough, for the length of 'hello+there'.
		 *    ($e, 'a.different.key') === ''
		 *
		 * @assert $e = $this->object->xencrypt('hello+there', 'caldwell.jason.key');
		 *    ('hello', 'caldwell.jason.different.key') === ''
		 *
		 * @assert $e = $this->object->xencrypt('hello+there', 'caldwell.jason.key');
		 *    ('hello', 'caldwell.jason.key') === ''
		 */
		public function xdecrypt($base64, $key = '')
		{
			$this->check_arg_types('string', 'string', func_get_args());

			if(strlen($e = $this->©string->base64_url_safe_decode($base64))
			   && preg_match('/^~xe(?:\:(?P<md5>[a-zA-Z0-9]+))?\|(?P<e>.*?)$/s', $e, $md5_e)
			) // This IS an XOR encrypted string?
			{
				if(strlen($md5_e['e']) && (empty($md5_e['md5']) || $md5_e['md5'] === md5($md5_e['e'])))
				{
					for($key = $this->key($key), $_i = 1, $string = ''; $_i <= strlen($md5_e['e']); $_i++)
					{
						$_char     = (string)substr($md5_e['e'], $_i - 1, 1);
						$_key_char = (string)substr($key, ($_i % strlen($key)) - 1, 1);
						$string .= chr(ord($_char) - ord($_key_char));
					}
					unset($_i, $_char, $_key_char);

					if(!strlen($string))
						throw $this->©exception(
							__METHOD__.'#failure', compact('string'),
							$this->i18n('String decryption failed (`string` is empty).')
						);
				}
				else return ($string = ''); // Empty string if validation fails.

				if(strlen($string = preg_replace('/^~xe\|/', '', $string, 1, $xe)) && $xe)
					return $string; // Looks good, we can return the decrypted string now.
				else return ($string = ''); // Empty string if decryption fails validation.
			}
			else return ''; // Empty string if decryption fails validation.
		}

		/**
		 * Generates an RSA-SHA1 signature.
		 *
		 * @param string  $string Input string/data, to be signed by this routine.
		 *
		 * @param string  $key Optional. Key used for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @return string An RSA-SHA1 signature string, else an exception is thrown.
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 * @throws exception If unable to generate an RSA-SHA1 signature, by any means.
		 */
		public function rsa_sha1_sign($string, $key = '')
		{
			$this->check_arg_types('string', 'string', func_get_args());

			$key = $this->_rsa_sha1_key_fix_wrappers($this->key($key));

			if(($signature = $this->_rsa_sha1_shell_sign($string, $key)))
				return $signature; // Most reliable.

			if($this->©commands->windows_possible() && is_file(($openssl = 'c:/openssl-win32/bin/openssl.exe')))
				if(($signature = $this->_rsa_sha1_shell_sign($string, $key, $openssl)))
					return $signature;

			if($this->©commands->windows_possible() && is_file(($openssl = 'c:/openssl-win64/bin/openssl.exe')))
				if(($signature = $this->_rsa_sha1_shell_sign($string, $key, $openssl)))
					return $signature;

			if($this->©function->is_possible('openssl_sign') && is_resource($private_key = openssl_pkey_get_private($key)))
			{
				openssl_sign($string, $signature, $private_key, OPENSSL_ALGO_SHA1);
				openssl_free_key($private_key);

				if($signature)
					return $signature;
			}
			if($this->©commands->windows_possible() && $this->©env->is_localhost())
				$windows_msg = $this->i18n(
					' Regarding Windows® `localhost` servers...'.
					' OpenSSL can be problematic on Windows® `localhost` servers, such as WAMP or EasyPHP.'.
					' On Windows®, you might need to install this alternative. See: `http://slproweb.com/products/Win32OpenSSL.html`.'.
					' Please make sure that you install it in this EXACT location: `c:/openssl-win32/bin/openssl.exe`.'.
					' On 64-bit systems, please make sure you install it here: `c:/openssl-win64/bin/openssl.exe`.'
				);
			else $windows_msg = ''; // No Windows® message otherwise.

			throw $this->©exception(
				__METHOD__.'#failure', NULL,
				$this->i18n(
					'Unable to generate an RSA-SHA1 signature.'.
					' Please make sure your installation of PHP is compiled with the OpenSSL extension.'.
					' The PHP function `openssl_sign()` is required by this routine.'
				).$windows_msg
			);
		}

		/**
		 * Generates an RSA-SHA1 signature from the command line.
		 *
		 * @param string  $string Input string/data, to be signed by this routine.
		 *
		 * @param string  $key Optional. Key used for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @param string  $openssl Optional. Defaults to `openssl`.
		 *    This is a full path to an OpenSSL executable.
		 *
		 * @return string An RSA-SHA1 signature string, else an empty string on failure.
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 * @throws exception If ``$openssl`` is empty.
		 *
		 * @assert ('hello') !empty TRUE
		 */
		public function _rsa_sha1_shell_sign($string, $key = '', $openssl = 'openssl')
		{
			$this->check_arg_types('string', 'string', 'string:!empty', func_get_args());

			if($this->©commands->possible())
			{
				$sys_temp_dir = $this->©dirs->get_sys_temp_dir();

				if(file_put_contents(($string_file = $sys_temp_dir.'/'.md5($this->©string->unique_id().'rsa-sha1-string').'.tmp'), $string)
				   && file_put_contents(($private_key_file = $sys_temp_dir.'/'.md5($this->©string->unique_id().'rsa-sha1-private-key').'.tmp'), $this->key($key))
				   && file_put_contents(($rsa_sha1_sig_file = $sys_temp_dir.'/'.md5($this->©string->unique_id().'rsa-sha1-sig').'.tmp'), '') === 0
				) // If we can write all of these files properly.
				{
					$this->©commands->exec(
						$this->©commands->esa($openssl).
						' sha1 -sign '.$this->©commands->esa($private_key_file).
						' -out '.$this->©commands->esa($rsa_sha1_sig_file).
						' '.$this->©commands->esa($string_file)
					);

					$signature = file_get_contents($rsa_sha1_sig_file);

					unlink($string_file);
					unlink($private_key_file);
					unlink($rsa_sha1_sig_file);

					if($signature)
						return $signature;
				}
			}
			return ''; // Default return value.
		}

		/**
		 * Fixes incomplete private key wrappers for RSA-SHA1 signing.
		 *
		 * @param string $key The secret key to be used in an RSA-SHA1 signature.
		 *
		 * @return string Key with incomplete wrappers corrected, if possible.
		 *
		 * @throws exception If invalid types are passed through arguments lists.
		 *
		 * @see http://www.faqs.org/qa/qa-14736.html
		 *
		 * @assert ('hello') === '-----BEGIN RSA PRIVATE KEY-----'."\n".'hello'."\n".'-----END RSA PRIVATE KEY-----'
		 * @assert ('-----BEGIN RSA PRIVATE KEY-----."\n".'hello') === '-----BEGIN RSA PRIVATE KEY-----'."\n".'hello'."\n".'-----END RSA PRIVATE KEY-----'
		 * @assert ('hello'."\n".'-----END RSA PRIVATE KEY-----') === '-----BEGIN RSA PRIVATE KEY-----'."\n".'hello'."\n".'-----END RSA PRIVATE KEY-----'
		 *
		 * @assert $invalid_key = '-----INVALID-----'."\n".'hello'."\n".'-----INVALID-----';
		 * // WARNING: We do NOT fix keys with more than two boundary lines (i.e. with multiple signatures).
		 *    ($invalid_key."\n\n".$invalid_key) === $invalid_key."\n\n".$invalid_key
		 */
		public function _rsa_sha1_key_fix_wrappers($key)
		{
			$this->check_arg_types('string', func_get_args());

			$wrappers = array // Wrappers for an RSA private key.
			(
				'begin' => '-----BEGIN RSA PRIVATE KEY-----',
				'end'   => '-----END RSA PRIVATE KEY-----'
			);
			if(strpos($key, $wrappers['begin']) === FALSE || strpos($key, $wrappers['end']) === FALSE)
			{
				foreach(($lines = $this->©strings->trim_deep(preg_split("/[\r\n]+/", trim($key)))) as $_line => $_value)
					if(strpos($_value, '-') === 0) // Begins with a boundary identifier (i.e. a hyphen `-`)?
					{
						$_boundaries = (empty($_boundaries)) ? 1 : $_boundaries + 1;
						unset($lines[$_line]); // Remove this boundary line.
					}
				// Do NOT modify keys with more than 2 boundaries.
				if(empty($_boundaries) || $_boundaries <= 2)
					$key = $wrappers['begin']."\n".
					       implode("\n", $lines)."\n".
					       $wrappers['end'];
			}
			return $key; // Return key now.
		}

		/**
		 * Generates an HMAC-SHA1 signature.
		 *
		 * @param string  $string Input string/data, to be signed by this routine.
		 *
		 * @param string  $key Optional. Key used for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @param boolean $raw Optional. Defaults to a FALSE value.
		 *    If true, the signature is returned as raw binary data, as opposed to lowercase hexits (the default).
		 *
		 * @return string An HMAC-SHA1 signature string. Always 40 characters in length (URL safe).
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 */
		public function hmac_sha1_sign($string, $key = '', $raw = FALSE)
		{
			$this->check_arg_types('string', 'string', 'boolean', func_get_args());

			return hash_hmac('sha1', $string, $this->key($key), $raw);
		}

		/**
		 * Generates an HMAC-SHA256 signature.
		 *
		 * @param string  $string Input string/data, to be signed by this routine.
		 *
		 * @param string  $key Optional. Key used for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @param boolean $raw Optional. Defaults to a FALSE value.
		 *    If true, the signature is returned as raw binary data, as opposed to lowercase hexits (the default).
		 *
		 * @return string An HMAC-SHA256 signature string. Always 64 characters in length (URL safe).
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 */
		public function hmac_sha256_sign($string, $key = '', $raw = FALSE)
		{
			$this->check_arg_types('string', 'string', 'boolean', func_get_args());

			return hash_hmac('sha256', $string, $this->key($key), $raw);
		}

		/**
		 * Generates an HMAC-SHA512 signature.
		 *
		 * @param string  $string Input string/data, to be signed by this routine.
		 *
		 * @param string  $key Optional. Key used for encryption.
		 *    Defaults to the one configured for the plugin.
		 *
		 * @param boolean $raw Optional. Defaults to a FALSE value.
		 *    If true, the signature is returned as raw binary data, as opposed to lowercase hexits (the default).
		 *
		 * @return string An HMAC-SHA512 signature string. Always 128 characters in length (URL safe).
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 */
		public function hmac_sha512_sign($string, $key = '', $raw = FALSE)
		{
			$this->check_arg_types('string', 'string', 'boolean', func_get_args());

			return hash_hmac('sha512', $string, $this->key($key), $raw);
		}
	}
}