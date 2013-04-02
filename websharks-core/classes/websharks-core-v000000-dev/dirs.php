<?php
/**
 * Directory Utilities.
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
		 * Directory Utilities.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @assert ($GLOBALS[__NAMESPACE__])
		 */
		class dirs extends framework
		{
			/**
			 * Normalizes directory separators.
			 *
			 * @return array {@inheritdoc}
			 *
			 * @see \websharks_core_v000000_dev::n_dir_seps()
			 * @inheritdoc \websharks_core_v000000_dev::n_dir_seps()
			 */
			public function n_seps() // Arguments are NOT listed here.
				{
					return call_user_func_array('\\'.__NAMESPACE__.'::n_dir_seps', func_get_args());
				}

			/**
			 * Strips a trailing `/app_data/` sub-directory.
			 *
			 * @param string $path Directory or file path.
			 *
			 * @return string Directory or file path without `/app_data/`.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert ('/path/to/dir\\app_data/') === '/path/to/dir'
			 */
			public function rtrim_app_data($path)
				{
					$this->check_arg_types('string', func_get_args());

					return preg_replace('/\/app_data$/', '', $this->n_seps($path));
				}

			/**
			 * Basename from a full directory or file path.
			 *
			 * @param string $path Directory or file path.
			 *
			 * @return string Basename, including a possible `/app_data/` directory.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert ('/path/to/dir/app_data\\') === 'dir/app_data'
			 * @assert ('/path/to/dir/app_data/') === 'dir/app_data'
			 * @assert ('/path/to/dir/app_data/dir') === 'dir'
			 */
			public function get_base_app_data($path)
				{
					$this->check_arg_types('string', func_get_args());

					$path = preg_replace('/\/app_data$/', '', $this->n_seps($path), 1, $app_data);

					return basename($path).(($app_data) ? '/app_data' : '');
				}

			/**
			 * Shortens a directory or file path to its relative location from `DOCUMENT_ROOT`.
			 *
			 * @param string  $to The full directory or file path, which this routine will build a relative path ``$to``.
			 *
			 * @param boolean $try_realpaths Defaults to TRUE. When TRUE, try to acquire ``realpath()``,
			 *    thereby resolving all relative paths and/or symlinks in `DOCUMENT_ROOT` and ``$to``.
			 *
			 * @param boolean $use_win_diff_drive_jctn Defaults to TRUE. When TRUE, we'll try to work around issues with different drives on Windows®,
			 *    by attempting to create a directory junction between the two different drives; so a relative path can be formulated properly.
			 *
			 * @return string String with relative path to: ``$to`` (from: `DOCUMENT_ROOT`), else an empty string on failure.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If `DOCUMENT_ROOT` is empty, or is NOT a string.
			 *
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/path/to/a/dir/') === 'path/to/a/dir'
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/path/to/a/file.php') === 'path/to/a/file.php'
			 */
			public function get_doc_root_path($to, $try_realpaths = TRUE, $use_win_diff_drive_jctn = TRUE)
				{
					$this->check_arg_types('string', 'boolean', 'boolean', func_get_args());

					if($this->©string->¤is_not_empty($doc_root = $this->©vars->_SERVER('DOCUMENT_ROOT')))
						return $this->get_rel_path($doc_root, $to, $try_realpaths, $use_win_diff_drive_jctn);

					throw $this->©exception(
						__METHOD__.'#doc_root_missing', get_defined_vars(),
						$this->i18n('Invalid and/or empty `DOCUMENT_ROOT` (expecting string NOT empty).')
					);
				}

			/**
			 * Shortens a directory or file path to its relative location.
			 *
			 * @param string  $from The full directory or file path to calculate a relative path ``$from``.
			 *
			 * @param string  $to The full directory or file path, which this routine will build a relative path ``$to``.
			 *
			 * @param boolean $try_realpaths Defaults to TRUE; try to acquire ``realpath()`` for both ``$from`` and ``$to``.
			 *
			 * @param boolean $use_win_diff_drive_jctn Defaults to TRUE. When TRUE, we'll try to work around issues with different drives on Windows®,
			 *    by attempting to create a Directory Junction between the two different drives; so a relative path can be formulated properly.
			 *
			 * @return string String with relative path to: ``$to`` (from: ``$from``), else an empty string on failure.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If Windows® drive issues cannot be resolved in any way.
			 *
			 * @assert ($_SERVER['DOCUMENT_ROOT'], $_SERVER['DOCUMENT_ROOT'].'/wp-content/') === 'wp-content'
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/', $_SERVER['DOCUMENT_ROOT'].'/wp-content/') === '../'
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/index.php', $_SERVER['DOCUMENT_ROOT'].'/wp-content/index.php') === '../index.php'
			 * @assert ('/path/to/a/b/c/', '/path/to/a/b/c/d/e/file.php') === 'd/e/file.php'
			 * @assert ('/path/to/a/b/c/', '/path/to/a/file.php') === '../../file.php'
			 */
			public function get_rel_path($from, $to, $try_realpaths = TRUE, $use_win_diff_drive_jctn = TRUE)
				{
					$this->check_arg_types('string', 'string', 'boolean', 'boolean', func_get_args());

					if(!$from || !$to) // Nothing to do.
						return ''; // Default return value.

					if($try_realpaths && ($_real_from = realpath($from)) && ($_real_to = realpath($to)))
						{
							$from = $_real_from; // Real path on server.
							$to   = $_real_to; // Use real path on server.
						}
					unset($_real_from, $_real_to); // Housekeeping.

					$from = (is_file($from) || $this->©file->has_extension($from)) ? dirname($from) : $from;
					$from = preg_split('/\//', $this->n_seps($from));
					$to   = preg_split('/\//', $this->n_seps($to));

					if($this->©env->is_windows()) // Handle Windows® drive issues here.
						{
							if(preg_match('/^(?P<drive_letter>[A-Z])\:$/i', $from[0], $_m))
								$_from_drive = $_m['drive_letter'];

							if(preg_match('/^(?P<drive_letter>[A-Z])\:$/i', $to[0], $_m))
								$_to_drive = $_m['drive_letter'];

							if(!empty($_from_drive) && empty($_to_drive))
								{
									$_to_drive = $_from_drive;
									if(empty($to[0]))
										$to[0] = $_to_drive.':';
									else array_unshift($to, $_to_drive.':');
								}
							else if(!empty($_to_drive) && empty($_from_drive))
								{
									$_from_drive = $_to_drive;
									if(empty($from[0]))
										$from[0] = $_from_drive.':';
									else array_unshift($from, $_from_drive.':');
								}
							if($use_win_diff_drive_jctn) // Attempt to create a Directory Junction (if needed)?
								{
									if(isset($_from_drive, $_to_drive) && strcasecmp($_from_drive, $_to_drive) !== 0)
										{
											$_core_ns_stub_with_dashes = $this->___instance_config->core_ns_stub_with_dashes;
											$_from_drive_jctn          = $_from_drive.':/'.$_core_ns_stub_with_dashes.'-'.$_to_drive.'-jctn';

											$_temp_dir = $this->get_temp_dir();
											if(strcasecmp($_from_drive, $_temp_dir[0]) === 0)
												$_temp_dir_jctn = $_temp_dir.'/'.$_core_ns_stub_with_dashes.'-'.$_to_drive.'-jctn';

											$_jctn = (is_dir($_from_drive_jctn)) ? $_from_drive_jctn : '';
											if(!$_jctn && !empty($_temp_dir_jctn) && is_dir($_temp_dir_jctn))
												$_jctn = $_temp_dir_jctn;

											if(!$_jctn) // A Directory Junction does NOT exist yet?
												{
													try // Try creating a Directory Junction on the ``$_from_drive``.
														{
															$_jctn = $this->create_win_jctn($_from_drive_jctn, $_to_drive.':/');
														}
													catch(exception $_exception) // Try temp directory.
														{
															if(!empty($_temp_dir_jctn)) try
																{
																	$_jctn = $this->create_win_jctn($_temp_dir_jctn, $_to_drive.':/');
																}
															catch(exception $_exception)
																{
																	// We'll handle below.
																}
															unset($_exception); // Housekeeping.
														}
													if(!$_jctn) throw $this->©exception(
														__METHOD__.'#windows_drive', get_defined_vars(),
														$this->i18n('Unable to generate a relative path across different Windows® drives.').
														sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $_from_drive_jctn, $_to_drive.':/')
													);
												}
											array_shift($to); // Shift drive off and use junction now.
											foreach(array_reverse(preg_split('/\//', $_jctn)) as $_jctn_dir)
												array_unshift($to, $_jctn_dir);
										}
								}
							else if(isset($_from_drive, $_to_drive) && strcasecmp($_from_drive, $_to_drive) !== 0)
								{
									throw $this->©exception(
										__METHOD__.'#windows_drive', get_defined_vars(),
										$this->i18n('Unable to generate a relative path across different Windows® drives.').
										sprintf($this->i18n(' Drive from: `%1$s`, drive to: `%2$s`.'), $_from_drive.':/', $_to_drive.':/')
									);
								}
							unset($_m, $_from_drive, $_to_drive, $_core_ns_stub_with_dashes, $_from_drive_jctn, $_temp_dir, $_temp_dir_jctn, $_jctn, $_jctn_dir);
						}
					foreach(array_keys($from) as $_depth) // Loop through each ``$from`` directory ``$_depth``.
						if(isset($from[$_depth], $to[$_depth]) && $from[$_depth] === $to[$_depth])
							unset($from[$_depth], $to[$_depth]);
						else break;

					$to = implode('/', $to);
					for($_depth = 0; $_depth < count($from); $_depth++)
						$to = '../'.$to;

					unset($_depth); // A little housekeeping.

					return $to; // Relative path.
				}

			/**
			 * Checks if a directory or file path is actually a link.
			 *
			 * @param string $dir_file Directory or file path (i.e. a possible symlink).
			 *
			 * @return boolean TRUE if ``$dir_file`` is a link; else FALSE.
			 */
			public function is_link($dir_file)
				{
					$this->check_arg_types('string', func_get_args());

					if(!$dir_file) return FALSE; // Catch empty values.

					$dir_file = $this->n_seps($dir_file);
					$realpath = (file_exists($dir_file)) ? $this->n_seps((string)realpath($dir_file)) : '';

					return ($dir_file && $realpath && $dir_file !== $realpath);
				}

			/**
			 * Creates a directory Junction in Windows®.
			 *
			 * @param string $jctn Directory location of the Junction (i.e. the symlink).
			 *
			 * @param string $target Target directory that this Junction will connect to.
			 *
			 * @return string Location of the Junction; else an empty string on failure.
			 *    If the Junction already exists as a link (and it already points to the ``$target``);
			 *    we simply return ``$jctn`` without any change (e.g. there is nothing more to do in that scenario).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$jctn`` exists already and it's an existing directory or file (i.e. NOT a link).
			 * @throws exception If ``$jctn`` exists and it's NOT writable (or we are unable to remove it before recreating).
			 * @throws exception If ``$target`` does NOT exist; or if it does exist, but it's NOT a directory.
			 * @throws exception If the Junction needs to be created; but Windows® commands are NOT possible.
			 * @throws exception If creation of a Directory Junction fails for any reason.
			 *
			 * @assert ('C:/websharks-core-jctn-tester', 'E:/') === 'C:/websharks-core-jctn-tester'
			 */
			public function create_win_jctn($jctn, $target)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', func_get_args());

					$jctn   = $this->n_seps($jctn);
					$target = $this->n_seps($target);

					$is_jctn_dir   = is_dir($jctn);
					$is_target_dir = is_dir($target);
					$is_jctn_link  = $this->is_link($jctn);

					$realpath_jctn   = ($is_jctn_dir) ? $this->n_seps((string)realpath($jctn)) : '';
					$realpath_target = ($is_target_dir) ? $this->n_seps((string)realpath($target)) : '';

					if($is_jctn_dir && $is_target_dir && $is_jctn_link && $realpath_jctn && $realpath_target && $realpath_jctn === $realpath_target)
						return $jctn; // It already exists; and it's already pointing to the proper location.

					if(!$is_target_dir)
						throw $this->©exception(
							__METHOD__.'#target_not_dir', get_defined_vars(),
							$this->i18n('Unable to create a Windows® Directory Junction. Invalid target.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);
					else if(!$this->©commands->windows_possible())
						throw $this->©exception(
							__METHOD__.'#not_possible', get_defined_vars(),
							$this->i18n('Not possible to create a Windows® Directory Junction.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);
					else if(!$this->delete_win_jctn($jctn))
						throw $this->©exception(
							__METHOD__.'#jctn_exists', get_defined_vars(),
							$this->i18n('Unable to create a Windows® Directory Junction. Already exists.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);
					$mklink_args = $this->©commands->mklink.' /J '.escapeshellarg($jctn).' '.escapeshellarg($target);
					$mklink      = $this->©commands->exec($mklink_args, '', TRUE);

					$mklink_status = $mklink['status'];
					$mklink_errors = $mklink['errors'];
					/** @var errors $mklink_errors */

					if($mklink_status !== 0 || $mklink_errors->exist())
						throw $this->©exception(
							__METHOD__.'#issue', get_defined_vars(),
							$this->i18n('Failed to create a Windows® Directory Junction.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target).
							sprintf($this->i18n(' The command: `%1$s`, returned a non-zero status or error; `mklink` said: `%2$s`'),
							        $mklink_args, $mklink_errors->get_message())
						);
					clearstatcache(); // Clear cache.

					if(!is_dir($jctn)) // Now we test this again.
						throw $this->©exception(
							__METHOD__.'#failure', get_defined_vars(),
							$this->i18n('Failed to create a Windows® Directory Junction.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);
					return $jctn; // It's a good day in Eureka!
				}

			/**
			 * Deletes a Windows® Directory Junction.
			 *
			 * @param string $jctn Directory location of the Junction (i.e. the symlink).
			 *
			 * @return boolean TRUE if Directory Junction is NOT a directory or file; or if it was deleted successfully.
			 *    Else this will throw an exception. We either return TRUE; or we throw an exception.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$jctn`` exists already and it's an actual directory/file (i.e. NOT a link).
			 * @throws exception If ``$jctn`` exists and it's NOT writable (or we are unable to remove it).
			 * @throws exception If deletion of a Directory Junction fails for any reason.
			 */
			public function delete_win_jctn($jctn)
				{
					$this->check_arg_types('string:!empty', func_get_args());

					$jctn         = $this->n_seps($jctn);
					$is_jctn_link = $this->is_link($jctn);
					$is_jctn_file = is_file($jctn);
					$is_jctn_dir  = is_dir($jctn);

					if(!$is_jctn_link && !$is_jctn_file && !$is_jctn_dir)
						return TRUE; // It's already gone :-)

					if($is_jctn_file)
						throw $this->©exception(
							__METHOD__.'#file', get_defined_vars(),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This is NOT a Directory Junction (it\'s a file): `%1$s`.'), $jctn)
						);
					else if(!$is_jctn_dir)
						throw $this->©exception(
							__METHOD__.'#not_dir', get_defined_vars(),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This is NOT a Directory Junction: `%1$s`.'), $jctn)
						);
					else if(!$is_jctn_link)
						throw $this->©exception(
							__METHOD__.'#not_link', get_defined_vars(),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This is NOT a Directory Junction: `%1$s`.'), $jctn)
						);

					if(!is_writable($jctn))
						throw $this->©exception(
							__METHOD__.'#permissions', get_defined_vars(),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This Directory Junction is NOT writable: `%1$s`.'), $jctn)
						);

					if(!$this->©commands->windows_possible())
						throw $this->©exception(
							__METHOD__.'#not_possible', get_defined_vars(),
							$this->i18n('Not possible to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' Please delete this Directory Junction: `%1$s`.'), $jctn)
						);
					$rmdir_args = $this->©commands->rmdir.' '.escapeshellarg(str_replace('/', '\\', $jctn));
					$rmdir      = $this->©commands->exec($rmdir_args, '', TRUE);

					$rmdir_status = $rmdir['status'];
					$rmdir_errors = $rmdir['errors'];
					/** @var errors $rmdir_errors */

					if($rmdir_status !== 0 || $rmdir_errors->exist())
						throw $this->©exception(
							__METHOD__.'#issue', get_defined_vars(),
							sprintf($this->i18n('The command: `%1$s`, returned a non-zero status or error. Rmdir said: `%2$s`'),
							        $rmdir_args, $rmdir_errors->get_message())
						);
					clearstatcache(); // Clear cache.

					return TRUE; // Default return value.
				}

			/**
			 * Gets a readable/writable temporary directory.
			 *
			 * @return string {@inheritdoc}
			 *
			 * @see \websharks_core_v000000_dev::get_temp_dir()
			 * @inheritdoc \websharks_core_v000000_dev::get_temp_dir()
			 *
			 * @throws exception If unable to find a readable/writable directory for any reason.
			 */
			public function get_temp_dir() // Arguments are NOT listed here.
				{
					if(($temp_dir = call_user_func_array('\\'.__NAMESPACE__.'::get_temp_dir', func_get_args())))
						return $temp_dir;

					throw $this->©exception(
						__METHOD__.'#missing', get_defined_vars(),
						$this->i18n('Unable to find a readable/writable temp directory.')
					);
				}

			/**
			 * Calculates the MD5 checksum for an entire directory recursively.
			 *
			 * @param string  $dir The directory we should begin with.
			 *
			 * @param boolean $update_checksum_file Defaults to a FALSE value.
			 *    If this is TRUE, the `checksum.txt` file in the root directory @ ``$depth`` zero, will be updated.
			 *    If the `checksum.txt` file does NOT exist yet, this routine will attempt to create it.
			 *
			 * @param string  $___root_dir Internal parameter. Defaults to an empty string, indicating the current ``$dir``.
			 *    Recursive calls to this method will automatically pass this value, indicating the main root directory value.
			 *
			 * @return string An MD5 checksum established collectively, based on all directories/files.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$dir`` is empty; is NOT a directory; or is not readable.
			 * @throws exception If ``$update_checksum_file`` is TRUE, ``$depth`` is zero,
			 *    and permission issues (of any kind) prevent the update or creation of the `checksum.txt` file.
			 *
			 * @see deps_x_websharks_core_v000000_dev::dir_checksum()
			 */
			public function checksum($dir, $update_checksum_file = FALSE, $___root_dir = '')
				{
					$this->check_arg_types('string:!empty', 'boolean', 'string', func_get_args());

					$checksums                = array(); // Initialize array.
					$dir                      = $this->n_seps((string)realpath($dir));
					$___root_dir              = (!$___root_dir) ? $dir : $___root_dir;
					$relative_dir             = preg_replace('/^'.preg_quote($___root_dir, '/').'(?:\/|$)/', '', $dir);
					$checksums[$relative_dir] = md5($relative_dir); // Relative directory checksum.

					if(!$dir || !is_dir($dir) || !is_readable($dir) || !($handle = opendir($dir)))
						throw $this->©exception(
							__METHOD__.'#cannot_read_dir', NULL,
							sprintf($this->i18n('Unable to read directory: `%1$s`'), $dir)
						);

					while(($entry = readdir($handle)) !== FALSE)
						if($entry !== '.' && $entry !== '..') // Ignore single/double dots.
							if($entry !== 'checksum.txt' || $dir !== $___root_dir) // Skip in root directory.
								{
									if(is_dir($dir.'/'.$entry))
										$checksums[$relative_dir.'/'.$entry] = $this->checksum($dir.'/'.$entry, FALSE, $___root_dir);
									else $checksums[$relative_dir.'/'.$entry] = md5($relative_dir.'/'.$entry.md5_file($dir.'/'.$entry));
								}
					closedir($handle); // Close directory handle now.

					ksort($checksums, SORT_STRING); // In case order changes from one server to another.

					$checksum = md5(implode('', $checksums)); // Collective checksum.

					if($update_checksum_file && $dir === $___root_dir)
						{
							if(!is_file($dir.'/checksum.txt') && !is_writable($dir))
								throw $this->©exception(
									__METHOD__.'#permission_issue', get_defined_vars(),
									sprintf($this->i18n('Need this directory to be writable: `%1$s`'), $dir)
								);
							else if(is_file($dir.'/checksum.txt') && !is_writable($dir.'/checksum.txt'))
								throw $this->©exception(
									__METHOD__.'#permission_issue', get_defined_vars(),
									sprintf($this->i18n('Need this file to be writable: `%1$s`'), $dir.'/checksum.txt')
								);
							file_put_contents($dir.'/checksum.txt', $checksum);
						}
					return $checksum; // Collective checksum.
				}

			/**
			 * Get cache directory (public or private).
			 *
			 * @param string $type The type of cache directory (public or private).
			 *    This MUST be passed using class constants ``framework::public_type`` or ``framework::private_type``.
			 *
			 * @param string $sub_dir Optional cache sub-directory path.
			 *
			 * @return string Full path to a readable/writable cache directory, else an exception is thrown on failure.
			 *    If the directory does NOT yet exist, it's created by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use class constants please.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 * @throws exception If the requested cache directory is NOT readable/writable, or CANNOT be created for any reason.
			 *
			 * @assert ('foo') throws exception
			 */
			public function get_cache_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string:!empty', 'string', func_get_args());

					// Check cache directory type.

					if(!in_array($type, array($this::public_type, $this::private_type), TRUE))
						throw $this->©exception(
							__METHOD__.'#invalid_type', get_defined_vars(),
							$this->i18n('Invalid cache type sub-directory. Expecting class contant for public or private type.').
							sprintf($this->i18n(' Instead got: `%1$s`.'), $type)
						);
					// Creates a possible ``$sub_dir`` appendage.

					if($sub_dir && ($sub_dir = $this->©strings->trim($this->n_seps($sub_dir), '', '/')))
						{
							if(strpos($sub_dir, '..') !== FALSE) // No relative paths.
								throw $this->©exception(
									__METHOD__.'#relative_paths', get_defined_vars(),
									$this->i18n('Expecting a sub-directory with NO relative paths.').
									sprintf($this->i18n(' Instead got: `%1$s`.'), $sub_dir)
								);
							$sub_dir = '/'.$sub_dir; // Add prefix so it can be appended easily.
						}
					// Clean these up and piece them together.

					$cache_dir          = $this->n_seps($this->___instance_config->plugin_data_dir.'/cache');
					$app_data_sub_dir   = ($type === $this::private_type && $this->©env->is_windows() && !$this->©env->is_apache()) ? '/app_data' : '';
					$cache_type_sub_dir = $this->n_seps($cache_dir.'/'.(($type === $this::private_type) ? 'private' : 'public').$app_data_sub_dir.$sub_dir);

					// Need to create the ``$cache_type_sub_dir``?

					if(!is_dir($cache_type_sub_dir))
						{
							mkdir($cache_type_sub_dir, 0775, TRUE);
							clearstatcache(); // Clear cache before checking again.

							if(!is_dir($cache_type_sub_dir) || !is_readable($cache_type_sub_dir) || !is_writable($cache_type_sub_dir))
								throw $this->©exception(
									__METHOD__.'#read_write_issues', get_defined_vars(),
									$this->i18n('Unable to create a readable/writable cache type sub-directory.').
									sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $cache_type_sub_dir)
								);
							if($type === $this::private_type && !is_file($cache_dir.'/private/.htaccess'))
								file_put_contents($cache_dir.'/private/.htaccess', 'deny from all');

							return $cache_type_sub_dir; // Created successfully!
						}
					// Else it exists. Is ``$cache_type_sub_dir`` still readable/writable?

					else if(!is_readable($cache_type_sub_dir) || !is_writable($cache_type_sub_dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to find a readable/writable cache type sub-directory.').
							sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $cache_type_sub_dir)
						);
					return $cache_type_sub_dir; // Everything still OK. It's a good day in Eureka!

				}

			/**
			 * Empties and deletes a cache directory (public or private).
			 *
			 * @param string $type The type of cache directory (public or private).
			 *    This MUST be passed using class constants ``framework::public_type`` or ``framework::private_type``.
			 *
			 * @param string $sub_dir Optional cache sub-directory path.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else FALSE.
			 *    Also returns TRUE if the directory is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use class constants please.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 *
			 * @throws exception See: ``get_cache_dir()`` for additional exceptions this may throw.
			 * @throws exception See: ``empty_and_remove()`` for additional exceptions this may throw.
			 *
			 * @assert ('public') === TRUE
			 * @assert ('private') === TRUE
			 * @assert ('foo') throws exception
			 */
			public function del_cache_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string:!empty', 'string', func_get_args());

					return $this->empty_and_remove($this->get_cache_dir($type, $sub_dir));
				}

			/**
			 * Get log directory (public or private).
			 *
			 * @param string $type The type of log directory (public or private).
			 *    This MUST be passed using class constants ``framework::public_type`` or ``framework::private_type``.
			 *
			 * @param string $sub_dir Optional log sub-directory path.
			 *
			 * @return string Full path to a readable/writable log directory, else an exception is thrown on failure.
			 *    If the directory does NOT yet exist, it's created by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use class constants please.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 * @throws exception If the requested log directory is NOT readable/writable, or CANNOT be created for any reason.
			 *
			 * @assert ('foo') throws exception
			 */
			public function get_log_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string:!empty', 'string', func_get_args());

					// Check log directory type.

					if(!in_array($type, array($this::public_type, $this::private_type), TRUE))
						throw $this->©exception(
							__METHOD__.'#invalid_type', get_defined_vars(),
							$this->i18n('Invalid log type sub-directory. Expecting class contant for public or private type.').
							sprintf($this->i18n(' Instead got: `%1$s`.'), $type)
						);
					// Creates a possible ``$sub_dir`` appendage.

					if($sub_dir && ($sub_dir = $this->©strings->trim($this->n_seps($sub_dir), '', '/')))
						{
							if(strpos($sub_dir, '..') !== FALSE) // No relative paths.
								throw $this->©exception(
									__METHOD__.'#relative_paths', get_defined_vars(),
									$this->i18n('Expecting a sub-directory with NO relative paths.').
									sprintf($this->i18n(' Instead got: `%1$s`.'), $sub_dir)
								);
							$sub_dir = '/'.$sub_dir; // Add prefix so it can be appended easily.

						}
					// Clean these up and piece them together.

					$logs_dir         = $this->n_seps($this->___instance_config->plugin_data_dir.'/logs');
					$app_data_sub_dir = ($type === $this::private_type && $this->©env->is_windows() && !$this->©env->is_apache()) ? '/app_data' : '';
					$log_type_sub_dir = $this->n_seps($logs_dir.'/'.(($type === $this::private_type) ? 'private' : 'public').$app_data_sub_dir.$sub_dir);

					// Need to create the ``$log_type_sub_dir``?

					if(!is_dir($log_type_sub_dir))
						{
							mkdir($log_type_sub_dir, 0775, TRUE);
							clearstatcache(); // Clear cache before checking again.

							if(!is_dir($log_type_sub_dir) || !is_readable($log_type_sub_dir) || !is_writable($log_type_sub_dir))
								throw $this->©exception(
									__METHOD__.'#read_write_issues', get_defined_vars(),
									$this->i18n('Unable to create a readable/writable log type sub-directory.').
									sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $log_type_sub_dir)
								);
							if($type === $this::private_type && !is_file($logs_dir.'/private/.htaccess'))
								file_put_contents($logs_dir.'/private/.htaccess', 'deny from all');

							return $log_type_sub_dir; // Created successfully!
						}
					// Else it exists. Is ``$log_type_sub_dir`` still readable/writable?

					else if(!is_readable($log_type_sub_dir) || !is_writable($log_type_sub_dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to find a readable/writable log type sub-directory.').
							sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $log_type_sub_dir)
						);
					return $log_type_sub_dir; // Everything still OK. It's a good day in Eureka!

				}

			/**
			 * Empties and deletes a log directory (public or private).
			 *
			 * @param string $type The type of log directory (public or private).
			 *    This MUST be passed using class constants ``framework::public_type`` or ``framework::private_type``.
			 *
			 * @param string $sub_dir Optional log sub-directory path.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else FALSE.
			 *    Also returns TRUE if the directory is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use class constants please.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 *
			 * @throws exception See: ``get_log_dir()`` for additional exceptions this may throw.
			 * @throws exception See: ``empty_and_remove()`` for additional exceptions this may throw.
			 *
			 * @assert ('public') === TRUE
			 * @assert ('private') === TRUE
			 * @assert ('foo') throws exception
			 */
			public function del_log_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string:!empty', 'string', func_get_args());

					return $this->empty_and_remove($this->get_log_dir($type, $sub_dir));
				}

			/**
			 * Gets a private media directory.
			 *
			 * @param string $sub_dir Optional private media sub-directory path.
			 *
			 * @return string Full path to a private readable/writable media directory, else an exception is thrown on failure.
			 *    If the directory does NOT yet exist, it's created by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 * @throws exception If the requested private media directory is NOT readable/writable, or CANNOT be created for any reason.
			 */
			public function get_private_media_dir($sub_dir = '')
				{
					$this->check_arg_types('string', func_get_args());

					// Creates a possible ``$sub_dir`` appendage.

					if($sub_dir && ($sub_dir = $this->©strings->trim($this->n_seps($sub_dir), '', '/')))
						{
							if(strpos($sub_dir, '..') !== FALSE) // No relative paths.
								throw $this->©exception(
									__METHOD__.'#relative_paths', get_defined_vars(),
									$this->i18n('Expecting a sub-directory with NO relative paths.').
									sprintf($this->i18n(' Instead got: `%1$s`.'), $sub_dir)
								);
							$sub_dir = '/'.$sub_dir; // Add prefix so it can be appended easily.

						}
					// Clean these up and piece them together.

					$media_dir        = $this->n_seps($this->___instance_config->plugin_data_dir.'/media');
					$app_data_sub_dir = ($this->©env->is_windows() && !$this->©env->is_apache()) ? '/app_data' : '';
					$media_sub_dir    = $this->n_seps($media_dir.$app_data_sub_dir.$sub_dir);

					// Need to create the ``$media_sub_dir``?

					if(!is_dir($media_sub_dir))
						{
							mkdir($media_sub_dir, 0775, TRUE);
							clearstatcache(); // Clear cache before checking again.

							if(!is_dir($media_sub_dir) || !is_readable($media_sub_dir) || !is_writable($media_sub_dir))
								throw $this->©exception(
									__METHOD__.'#read_write_issues', get_defined_vars(),
									$this->i18n('Unable to create a private readable/writable `media` directory.').
									sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $media_sub_dir)
								);
							if(!is_file($media_dir.'/.htaccess'))
								file_put_contents($media_dir.'/.htaccess', 'deny from all');

							return $media_sub_dir; // Created successfully!
						}
					// Else it exists. Is ``$media_sub_dir`` still readable/writable?

					else if(!is_readable($media_sub_dir) || !is_writable($media_sub_dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to find a private readable/writable media directory.').
							sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $media_sub_dir)
						);
					return $media_sub_dir; // Everything still OK. It's a good day in Eureka!

				}

			/**
			 * Empties and deletes a private media directory.
			 *
			 * @param string $sub_dir Optional media sub-directory path.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else FALSE.
			 *    Also returns TRUE if the directory is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 *
			 * @throws exception See: ``get_private_media_dir()`` for additional exceptions this may throw.
			 * @throws exception See: ``empty_and_remove()`` for additional exceptions this may throw.
			 */
			public function del_private_media_dir($sub_dir = '')
				{
					$this->check_arg_types('string', func_get_args());

					return $this->empty_and_remove($this->get_private_media_dir($sub_dir));
				}

			/**
			 * Recursively empties a directory and removes it via PHP.
			 *
			 * @param string $dir A full directory path to empty and remove.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else an exception is thrown.
			 *    Also returns TRUE if ``$dir`` is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$dir`` is NOT a string; or it's an empty string.
			 * @throws exception If the ``$dir`` given, is NOT readable/writable, or CANNOT be opened for any reason.
			 * @throws exception If any sub-directory or file within ``$dir`` is NOT readable/writable, or CANNOT be opened for any reason.
			 * @throws exception If any failure occurs during processing (e.g. we only return TRUE on success).
			 *
			 * @assert ($this->object->___instance_config->plugin_data_dir) === TRUE
			 * @assert ('foo') === TRUE
			 */
			public function empty_and_remove($dir)
				{
					$this->check_arg_types('string:!empty', func_get_args());

					$dir = $this->n_seps($dir);

					if(!file_exists($dir))
						return TRUE;

					else if(!is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#not_a_directory', get_defined_vars(),
							$this->i18n('Unable to remove a directory; argument value is NOT a directory path.').
							sprintf($this->i18n(' The invalid directory path given: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to remove a directory; not readable, due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $dir)
						);
					else if(!is_writable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to remove a directory; not writable, due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $dir)
						);
					else if(!($_open_dir = opendir($dir)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to remove a directory; cannot open, for some unknown reason.').
							sprintf($this->i18n(' Make this directory readable/writable please: `%1$s`.'), $dir)
						);

					while(($_dir_file = readdir($_open_dir)) !== FALSE) // Recursively delete all sub-directories/files.
						{
							if($_dir_file !== '.' && $_dir_file !== '..') // Ignore directory dots.
								{
									$_dir_file = $dir.'/'.$_dir_file; // We need the full path now.

									if(is_dir($_dir_file)) // Recursion for sub-directories.
										$this->empty_and_remove($_dir_file);
									else $this->©file->unlink($_dir_file);
								}
						}
					closedir($_open_dir); // Close resource handle now.
					unset($_open_dir, $_dir_file); // And also unset (e.g. unlocking this directory).

					$rmdir = rmdir($dir); // We should be able to delete the directory now.
					clearstatcache(); // This makes other routines aware. Very important to clear the cache.

					if(!$rmdir) throw $this->©exception(
						__METHOD__.'#possible_read_write_or_existing_resource_handle_issues', get_defined_vars(),
						sprintf($this->i18n('Unable to remove this directory: `%1$s`.'), $dir)
					);
					return TRUE; // It's a good day in Eureka!
				}

			/**
			 * Recursively copies a directory via PHP.
			 *
			 * @param string      $dir A full directory path to copy.
			 *
			 * @param string      $to A new directory path, to copy ``$dir`` to.
			 *    This new directory must NOT already exist. If it does, an exception is thrown.
			 *
			 * @param array       $exclusions Optional. Defaults to an empty array.
			 *
			 *    • A list of files/directories to ignore (using FNMATCH wildcard patterns).
			 *       The `FNM_PATHNAME` flag is NOT in use for any wildcard patterns you specify here.
			 *       A wildcard `*` WILL match directory separators (by default). See: ``$exclusions_fnm_pathname``.
			 *
			 *    • Special case handler for `.gitignore` files (VERY useful).
			 *       If ``$exclusions['.gitignore']`` === `/path/to/.gitignore`; we will attempt to process GIT via command line.
			 *       We use: `git ls-files --others --directory` to compile a complete list of exclusions automatically.
			 *       This way the copied directory will reflect only the files under GIT version control.
			 *       A `.gitignore` file MUST exist in the parent of your initial ``$dir``.
			 *
			 *    Regarding wildcard pattern matching...
			 *
			 *       • Files/directories will always include a leading slash `/` when checking exclusions.
			 *          We use an absolute relative path; starting with the initial ``$dir`` that you're copying from;
			 *          (in other words, relative from the parent directory of your initial ``$dir``).
			 *
			 *          Assuming your initial value for ``$dir`` is `/path/to/dir`.
			 *          And; assuming this file exists that you want to exclude: `/path/to/dir/sub-dir/file.php`.
			 *          You will need a pattern that matches this path: `/dir/sub-dir/file.php`.
			 *
			 *       • Directories will include a trailing slash when checking exclusions.
			 *
			 *          Assuming your initial value for ``$dir`` is `/path/to/dir`.
			 *          And; assuming this directory exists that you want to exclude: `/path/to/dir/sub-dir`.
			 *          You will need a pattern that matches this path: `/dir/sub-dir/`.
			 *
			 *       • File/directory paths obtained through `git ls-files --others --directory`;
			 *          are converted into wildcard patterns automatically by this routine.
			 *          A `.gitignore` file MUST exist in the parent of your initial ``$dir``.
			 *
			 * @param boolean     $exclusions_case_insensitive Defaults to a FALSE value.
			 *    If TRUE, wildcard pattern matching is NOT case sensitive.
			 *    The `FNM_CASEFOLD` flag is enabled if this is TRUE.
			 *
			 * @param boolean     $exclusions_fnm_pathname Optional. Defaults to a FALSE value.
			 *    If this is set to a value of TRUE; a wildcard `*` will NOT match a directory separator.
			 *    Use with caution. This is NOT compatible with `.gitignore` exclusions.
			 *
			 * @param null|string $___initial_dir Internal use only. Please do NOT pass this in directly.
			 *
			 * @return boolean TRUE if the directory was successfully copied, else an exception is thrown.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If the ``$dir`` given, is NOT a readable directory, or CANNOT be opened for any reason.
			 * @throws exception If any sub-directory or file within ``$dir`` is NOT readable, or CANNOT be opened for any reason.
			 * @throws exception If the ``$to`` directory already exists, or CANNOT be created by this routine for any reason.
			 *
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but the `.gitignore` file does not exist.
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but it's NOT in the initial ``$dir`` parent.
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but the `git` command is currently NOT possible.
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but `git` returns a non-zero status or error msg.
			 *
			 * @throws exception If a copy failure (of any kind) occurs (e.g. we are NOT successful).
			 *
			 * @see \websharks_core_v000000_dev\strings\in_wildcard_patterns()
			 * @see http://linux.die.net/man/3/fnmatch The underlying functionality applied to exclusions.
			 *
			 * @WARNING This routine can become resource intensive on large directories.
			 *    See: {@link \websharks_core_v000000_dev\env\maximize_time_memory_limits()}
			 *
			 * @assert ($this->object->___instance_config->plugin_dir, $this->object->___instance_config->plugin_dir.'-copy') === TRUE
			 */
			public function copy_to($dir, $to, $exclusions = array(), $exclusions_case_insensitive = FALSE, $exclusions_fnm_pathname = FALSE, $___initial_dir = NULL)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', 'array', 'boolean', 'boolean', array('null', 'string'), func_get_args());

					$dir = $this->n_seps($dir);
					$to  = $this->n_seps($to);

					if(!is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', get_defined_vars(),
							$this->i18n('Unable to copy a directory (source `dir` missing).').
							sprintf($this->i18n('Non-existent source directory: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to copy a directory; not readable due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable please: `%1$s`.'), $dir)
						);
					else if(!($_open_dir = opendir($dir)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to copy a directory; cannot open for some unknown reason.').
							sprintf($this->i18n(' Make this directory readable please: `%1$s`.'), $dir)
						);

					if(file_exists($to))
						throw $this->©exception(
							__METHOD__.'#destination_to_exists', get_defined_vars(),
							$this->i18n('Unable to copy a directory; destination already exists.').
							sprintf($this->i18n(' Please delete this file or directory: `%1$s`.'), $to)
						);
					else if(!is_writable(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to copy a directory; destination not writable due to permission issues.').
							sprintf($this->i18n(' Need this directory to be writable please: `%1$s`.'), dirname($to))
						);
					mkdir($to, 0755, TRUE); // Create the destination directory (with recursion).
					clearstatcache(); // Clear cache before re-testing; (also makes other routines aware it exists).

					if(!is_dir($to))
						throw $this->©exception(
							__METHOD__.'#mkdir_to_failure', get_defined_vars(),
							$this->i18n('Unable to copy a directory; destination creation failure.').
							sprintf($this->i18n(' Please create this directory: `%1$s`.'), $to)
						);

					if(!isset($___initial_dir)) $___initial_dir = $dir;

					if($dir === $___initial_dir // Only do this ONE time.
					   && isset($exclusions['.gitignore']) && $this->©string->is_not_empty($exclusions['.gitignore'])
					   && basename($exclusions['.gitignore']) === '.gitignore' // It MUST actually be a `.gitignore` file.
					) // This special array element is a `.gitignore` file; triggering this routine. Further validation below.
						{
							$_gitignore_file = $exclusions['.gitignore'];
							$_gitignore_dir  = $this->©dir->n_seps(dirname($_gitignore_file));
							unset($exclusions['.gitignore']); // Remove this now.

							if(!is_file($_gitignore_file))
								throw $this->©exception(
									__METHOD__.'#nonexistent_gitignore_file', get_defined_vars(),
									sprintf($this->i18n('Nonexistent `.gitignore` file: `%1$s`.'), $_gitignore_file)
								);
							else if($_gitignore_dir !== dirname($___initial_dir))
								throw $this->©exception(
									__METHOD__.'#invalid_gitignore_file', get_defined_vars(),
									sprintf($this->i18n('Invalid `.gitignore` file: `%1$s`.'), $_gitignore_file).
									sprintf($this->i18n('Your `.gitignore` file MUST exist here: `%1$s`.'), dirname($___initial_dir).'/.gitignore')
								);
							else if(!$this->©command->git_possible())
								throw $this->©exception(
									__METHOD__.'#git_command_not_possible', get_defined_vars(),
									sprintf($this->i18n('You specified the following `.gitignore` file: `%1$s`.'), $_gitignore_file).
									$this->i18n(' However, the `git` command is NOT possible in the current environment.')
								);
							else if($exclusions_fnm_pathname)
								throw $this->©exception(
									__METHOD__.'#gitignore_not_compatible_w/fnm_pathname', get_defined_vars(),
									sprintf($this->i18n('You specified the following `.gitignore` file: `%1$s`.'), $_gitignore_file).
									$this->i18n(' You\'re attempting to mix `.gitignore` functionality w/ `FNM_PATHNAME`; these are NOT compatible.')
								);
							$_gitignore_files = $this->©command->git('ls-files --others --directory', $_gitignore_dir);

							if($_gitignore_files) // Do we have output (e.g. a list of ignored files)?
								{
									foreach(preg_split('/['."\r\n".']+/', $_gitignore_files, NULL, PREG_SPLIT_NO_EMPTY) as $_path)
										// No need to normalize directory separators here; GIT already does that for us.
										// Directories returned by GIT always include a trailing slash (easy to identify).
										// The list provided by GIT does NOT include leading slashes though (we add those).
										$exclusions[] = (substr($_path, -1) === '/') ? '/'.$_path.'*' : '/'.$_path;
								}
							unset($_gitignore_file, $_gitignore_dir, $_gitignore_files, $_path);
						}
					while(($_dir_file = readdir($_open_dir)) !== FALSE) // Recursively copy all sub-directories/files.
						{
							if($_dir_file !== '.' && $_dir_file !== '..') // Ignore directory dots.
								{
									$_dir_file                   = $dir.'/'.$_dir_file;
									$_dir_file_is_dir            = is_dir($_dir_file);
									$_dir_file_abs_relative_path = '/'.preg_replace('/^'.preg_quote(dirname($___initial_dir), '/').'\//', '', $_dir_file);
									$_dir_file_abs_relative_path .= ($_dir_file_is_dir) ? '/' : ''; // Trailing dir separator on actual directories.
									$_dir_file_to = $to.'/'.basename($_dir_file); // New copy location.

									if(!$exclusions || !$this->©string->in_wildcard_patterns($_dir_file_abs_relative_path, $exclusions, $exclusions_case_insensitive, FALSE, (($exclusions_fnm_pathname) ? FNM_PATHNAME : NULL)))
										{ // Only if this directory/file has NOT been excluded via the ``$exclusions`` array.

											if($_dir_file_is_dir) // Recursive sub-directories.
												$this->copy_to($_dir_file, $_dir_file_to, $exclusions, $exclusions_case_insensitive, $exclusions_fnm_pathname, $___initial_dir);
											else $this->©file->copy_to($_dir_file, $_dir_file_to);
										}
								}
						}
					closedir($_open_dir); // Close directory resource handle before we unset these.
					unset($_open_dir, $_dir_file, $_dir_file_is_dir, $_dir_file_abs_relative_path, $_dir_file_to);
					clearstatcache(); // This makes other routines aware. Very important to clear the cache.

					return TRUE; // It's a good day in Eureka!
				}

			/**
			 * Creates a PHAR (PHP Archive) file from an entire directory.
			 *
			 * @param string       $dir The directory we want to create a PHAR file for.
			 *
			 * @param string       $to The new PHAR file location; with a `.phar` extension.
			 *
			 * @param string       $stub_file Stub file path. The contents of this stub file will be used as
			 *    the stub for the resulting PHAR file. Required for all PHARs created by this routine.
			 *
			 * @param boolean      $strip_ws Optional. Defaults to a TRUE value (highly recommended).
			 *    If this is TRUE; any PHP files in the archive will be further reduced in filesize by this routine.
			 *    This is made possible by the ``php_strip_whitespace()`` function.
			 *
			 * @param boolean      $compress Optional. Defaults to a TRUE value (highly recommended).
			 *    If this is TRUE; any compressable files in the archive will be further reduced in filesize.
			 *
			 * @param string|array $compressable_extensions Optional. An array of GZIP-compressable extensions.
			 *    This will default to only those which are 100% webPhar-compatible: ``array('php', 'phps')``.
			 *    Or, you can provide your own array of compressable extensions.
			 *
			 * @param string       $is_phar_var_suffix Optional. Defaults to `stub`.
			 *    A global variable at the top of your PHAR stub file will be declared as follows.
			 *    ``$GLOBALS['is_phar_'.$is_phar_var_suffix] = 'phar://'.__FILE__;`` (just a helpful identifier).
			 *
			 * @return string The new PHAR file path; else an exception is thrown on any type of failure.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$dir`` is empty, does NOT exist; or is NOT readable for any reason.
			 * @throws exception If ``$to`` is empty, or is NOT specified with a `.phar` extension.
			 * @throws exception If ``$to`` parent directory does NOT exist; or is not writable.
			 * @throws exception If ``$stub_file`` is empty, does NOT exist; or is NOT readable for any reason.
			 * @throws exception If ``$compressable_extensions`` or ``$is_phar_var_suffix`` is empty.
			 * @throws \exception If any Phar class failures occur. The Phar class may throw exceptions.
			 * @throws exception On any type of failure (e.g. NOT successful).
			 *
			 * @WARNING This routine can become resource intensive on large directories.
			 *    Mostly because of the compression routines applied here intuitively. It takes some time.
			 *    See: {@link \websharks_core_v000000_dev\env\maximize_time_memory_limits()}
			 */
			public function phar_to($dir, $to, $stub_file, $strip_ws = TRUE, $compress = TRUE, $compressable_extensions = array('php', 'phps'), $is_phar_var_suffix = 'stub')
				{
					$this->check_arg_types('string:!empty', 'string:!empty', 'string:!empty', 'boolean', 'boolean', 'array:!empty', 'string:!empty', func_get_args());

					$dir = $this->n_seps($dir);
					$to  = $this->n_seps($to);

					if(!is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory (source `dir` missing).').
							sprintf($this->i18n('Non-existent source directory: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory; not readable, due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable please: `%1$s`.'), $dir)
						);

					if(file_exists($to))
						throw $this->©exception(
							__METHOD__.'#existing_phar', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory; destination PHAR file already exists.').
							sprintf($this->i18n(' Please delete this file first: `%1$s`.'), $to)
						);
					else if(!preg_match('/\.phar$/i', $to))
						throw $this->©exception(
							__METHOD__.'#invalid_phar_file', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory; invalid destination PHAR file.').
							sprintf($this->i18n(' Please use a `.phar` extension instead of: `%1$s`.'), $to)
						);
					else if(!is_dir(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#phar_to_dir_missing', get_defined_vars(),
							$this->i18n('Destination PHAR directory does NOT exist yet.').
							sprintf($this->i18n(' Please check this directory: `%1$s`.'), dirname($to))
						);
					else if(!is_writable(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory; destination not writable due to permission issues.').
							sprintf($this->i18n(' Need this directory to be writable please: `%1$s`.'), dirname($to))
						);
					else if(!\Phar::canWrite())
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory; PHP configuration does NOT allow write access.').
							$this->i18n(' Need this INI setting please: `phar.readonly = 0`.')
						);

					if(!is_file($stub_file))
						throw $this->©exception(
							__METHOD__.'#missing_stub_file', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory; missing stub file.').
							sprintf($this->i18n(' File does NOT exist: `%1$s`.'), $stub_file)
						);
					else if(!is_readable($stub_file))
						throw $this->©exception(
							__METHOD__.'#stub_file_issues', get_defined_vars(),
							$this->i18n('Unable to PHAR a directory; permission issues with stub file.').
							sprintf($this->i18n(' Need this file to be writable please: `%1$s`.'), $stub_file)
						);
					// Phar classes throw exceptions on failure.

					$_stub_file_is_phar_var = '$GLOBALS[\'is_phar_'.$is_phar_var_suffix.'\'] = \'phar://\'.__FILE__;';
					$_stub_file_contents    = ($strip_ws) ? php_strip_whitespace($stub_file) : file_get_contents($stub_file);
					$_stub_file_contents    = $this->©string->ireplace_once('<?php', '<?php '.$_stub_file_is_phar_var.' ', $_stub_file_contents);

					$_phar = new \Phar($to, $this->iteration_flags());
					$_phar->startBuffering(); // Don't create file yet (wait until we're done here).
					$_phar->setStub($_stub_file_contents); // Defines the stub for this PHAR file.

					if(!$strip_ws && !$compress) $_phar->buildFromDirectory($dir); // Simple.

					else // Stripping or compressing takes more work, but worth the effort :-)
						{
							$_strippable_extensions         = array('php');
							$_regex_compressable_extensions = $this->©string->preg_quote_deep($compressable_extensions, '/');
							$_regex_compressable_extensions = '/\.(?:'.implode('|', $_regex_compressable_extensions).')$/i';
							$_temp_dir                      = $this->get_temp_dir().'/'.$this->©string->unique_id().'-'.basename($dir);

							$this->copy_to($dir, $_temp_dir);
							$_temp_dir_iterator = $this->iterate($_temp_dir);

							if($strip_ws) foreach($_temp_dir_iterator as $_dir_file)
								{
									if(!$_dir_file->isFile()) continue;

									$_path      = $_dir_file->getPathname();
									$_phar_path = $_dir_file->getSubPathName();
									$_extension = $this->©file->extension($_path);

									if(in_array($_extension, $_strippable_extensions, TRUE))
										file_put_contents($_path, php_strip_whitespace($_path));
								}
							$_phar->buildFromDirectory($_temp_dir, $_regex_compressable_extensions);
							if($compress && $_phar->count()) // Compressing files?
								$_phar->compressFiles(\Phar::GZ);

							foreach($_temp_dir_iterator as $_dir_file) // Everything else.
								{
									if(!$_dir_file->isFile()) continue;

									$_path      = $_dir_file->getPathname();
									$_phar_path = $_dir_file->getSubPathName();
									$_extension = $this->©file->extension($_path);

									if(!in_array($_extension, $compressable_extensions, TRUE))
										$_phar->addFile($_path, $_phar_path);
								}
						}
					$_phar->stopBuffering(); // Write to disk now.

					unset($_phar, $_stub_file_is_phar_var, $_stub_file_contents, $_strippable_extensions, $_regex_compressable_extensions);
					unset($_temp_dir_iterator, $_dir_file, $_path, $_phar_path, $_extension);
					if(isset($_temp_dir)) // A little more housekeeping now.
						$this->empty_and_remove($_temp_dir);
					unset($_temp_dir);

					return $to; // It's a good day in Eureka!
				}

			/**
			 * Recursively ZIPs a directory via PHP.
			 *
			 * @param string $dir A full directory path to ZIP.
			 *
			 * @param string $to A new ZIP file path — to ZIP ``$dir`` into.
			 *    The directory this lives in MUST already exist and be writable.
			 *    If this file already exists, an exception will be thrown.
			 *
			 * @return string New ZIP file location; else an exception is thrown.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If the ``$dir`` is NOT a readable directory, or CANNOT be zipped for any reason.
			 * @throws exception If the ``$to`` ZIP already exists, or CANNOT be created by this routine for any reason.
			 * @throws exception If the ``$to`` ZIP does NOT end with the proper `.zip` extension.
			 * @throws exception If the ``$to`` ZIP parent directory does NOT exist or is not writable.
			 * @throws \exception The PclZip class may throw exceptions of it's own here.
			 */
			public function zip_to($dir, $to)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', func_get_args());

					$dir = $this->n_seps($dir);
					$to  = $this->n_seps($to);

					if(!class_exists('\\PclZip'))
						include_once ABSPATH.'wp-admin/includes/class-pclzip.php';

					if(!is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', get_defined_vars(),
							$this->i18n('Unable to ZIP a directory (source `dir` missing).').
							sprintf($this->i18n(' Non-existent source directory: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to ZIP a directory; not readable; due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable please: `%1$s`.'), $dir)
						);

					if(file_exists($to))
						throw $this->©exception(
							__METHOD__.'#existing_zip', get_defined_vars(),
							$this->i18n('Destination ZIP exists; it MUST first be deleted please.').
							sprintf($this->i18n(' Please check this ZIP archive: `%1$s`.'), $to)
						);
					else if(substr($to, -4) !== '.zip')
						throw $this->©exception(
							__METHOD__.'#invalid_zip', get_defined_vars(),
							$this->i18n('Invalid ZIP extension. The destination must end with `.zip`.').
							sprintf($this->i18n(' Instead got: `%1$s`.'), $to)
						);
					else if(!is_dir(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#zip_to_dir_missing', get_defined_vars(),
							$this->i18n('Destination ZIP directory does NOT exist yet.').
							sprintf($this->i18n(' Please check this directory: `%1$s`.'), dirname($to))
						);
					else if(!is_writable(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#zip_to_dir_permissions', get_defined_vars(),
							$this->i18n('Destination ZIP directory is not writable.').
							sprintf($this->i18n(' Please check permissions on this directory: `%1$s`.'), dirname($to))
						);

					$archive = new \PclZip($to);
					if(!$archive->create($dir, PCLZIP_OPT_REMOVE_PATH, dirname($dir)))
						throw $this->©exception(
							__METHOD__.'#pclzip_archive_failure#'.$archive->errorCode(), get_defined_vars(),
							sprintf($this->i18n('PclZip archive failure: `%1$s`.'), $archive->errorInfo(TRUE))
						);
					return $to; // It's a good day in Eureka!
				}

			/**
			 * Rename a directory.
			 *
			 * @param string $dir A full directory path.
			 * @param string $to A new full directory path.
			 *
			 * @return string Path to new directory location; else an exception is thrown.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If the ``$dir`` does NOT exist; or is NOT a readable/writable directory.
			 * @throws exception If the ``$to`` directory already exists (either as a file or directory).
			 * @throws exception If the ``$to`` parent directory does NOT exist or is NOT writable.
			 * @throws exception If the underlying call to PHP's ``rename()`` function fails for any reason.
			 */
			public function rename_to($dir, $to)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', func_get_args());

					$dir = $this->n_seps($dir);
					$to  = $this->n_seps($to);

					if(!is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', get_defined_vars(),
							$this->i18n('Unable to rename a directory (source `dir` missing).').
							sprintf($this->i18n(' Non-existent source directory: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to rename a directory; not readable; due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable please: `%1$s`.'), $dir)
						);
					else if(!is_writable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', get_defined_vars(),
							$this->i18n('Unable to rename a directory; not writable; due to permission issues.').
							sprintf($this->i18n(' Need this directory to be writable please: `%1$s`.'), $dir)
						);

					if(file_exists($to))
						throw $this->©exception(
							__METHOD__.'#destination_exists', get_defined_vars(),
							$this->i18n('Destination exists; it MUST first be deleted please.').
							sprintf($this->i18n(' Please check this file or directory: `%1$s`.'), $to)
						);
					else if(!is_dir(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#destination_dir_missing', get_defined_vars(),
							$this->i18n('Destination\'s parent directory does NOT exist yet.').
							sprintf($this->i18n(' Please check this directory: `%1$s`.'), dirname($to))
						);
					else if(!is_writable(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#destination_dir_permissions', get_defined_vars(),
							$this->i18n('Destination\'s directory is not writable.').
							sprintf($this->i18n(' Please check permissions on this directory: `%1$s`.'), dirname($to))
						);

					if(!rename($dir, $to))
						throw $this->©exception(
							__METHOD__.'#rename_failure', get_defined_vars(),
							sprintf($this->i18n('Rename failure. Could NOT rename: `%1$s`; to: `%2$s`.'), $dir, $to)
						);
					return $to; // It's a good day in Eureka!
				}

			/**
			 * Checks the current plugin's data directory.
			 *
			 * @return boolean TRUE if the current plugin's data directory exists, and is readable/writable; else FALSE.
			 *    If the directory does NOT exist, this routine will attempt to create it before checking.
			 *
			 * @assert () === TRUE
			 */
			public function data_dir_readable_writable()
				{
					$data_dir = $this->___instance_config->plugin_data_dir;

					if(!is_dir($data_dir) && is_writable(dirname($data_dir)))
						{
							mkdir($data_dir, 0775);
							clearstatcache(); // Clear cache.
						}
					if(is_dir($data_dir) && is_readable($data_dir) && is_writable($data_dir))
						return TRUE; // Yes, we have a good data directory now.

					return FALSE; // Default return value.
				}

			/**
			 * Gets all possible template directories (ordered by precedence).
			 *
			 * @return array All possible template directories (ordered by precedence).
			 */
			public function where_templates_may_reside()
				{
					$dirs = array(
						get_stylesheet_directory().'/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						get_template_directory().'/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						$this->___instance_config->plugin_pro_dir.'/templates/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						$this->___instance_config->plugin_dir.'/templates/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						dirname(dirname(dirname(__FILE__))).'/templates/'
					);
					return $this->apply_filters('where_templates_may_reside', $dirs);
				}

			/**
			 * A recursive directory iterator.
			 *
			 * @param string       $dir An absolute directory path.
			 *
			 * @param null|integer $x_flags The defaults are recommended; but extra flags can be passed in.
			 *
			 * @param null|integer $flags The defaults are recommended; but specific flags can be passed in if you prefer.
			 *    The difference between ``$x_flags`` and ``$flags``; is that ``$flags`` will override all defaults;
			 *    whereas ``$x_flags`` will simply add additional flags to the existing defaults.
			 *
			 * @return \RecursiveIteratorIterator|\RecursiveDirectoryIterator[]|\SplFileInfo[]
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$dir`` is NOT a directory.
			 */
			public function iterate($dir, $x_flags = NULL, $flags = NULL)
				{
					$this->check_arg_types('string:!empty', array('null', 'integer'), array('null', 'integer'), func_get_args());

					if(!is_dir($dir = $this->n_seps($dir)))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', get_defined_vars(),
							$this->i18n('Unable to iterate a directory (source `dir` missing).').
							sprintf($this->i18n(' Non-existent source directory: `%1$s`.'), $dir)
						);
					$flags = $this->iteration_flags($x_flags, $flags);

					return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, $flags));
				}

			/**
			 * Default directory iteration flags.
			 *
			 * @param null|integer $x_flags The defaults are recommended; but extra flags can be passed in.
			 *
			 * @param null|integer $flags The defaults are recommended; but specific flags can be passed in if you prefer.
			 *    The difference between ``$x_flags`` and ``$flags``; is that ``$flags`` will override all defaults;
			 *    whereas ``$x_flags`` will simply add additional flags to the existing defaults.
			 *
			 * @return integer Flags for ``\RecursiveDirectoryIterator``.
			 */
			public function iteration_flags($x_flags = NULL, $flags = NULL)
				{
					$this->check_arg_types(array('null', 'integer'), array('null', 'integer'), func_get_args());

					if(!isset($flags)) // Defaults.
						$flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF |
						         \FilesystemIterator::FOLLOW_SYMLINKS | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS;
					if(isset($x_flags)) $flags = $flags | $x_flags;

					return $flags;
				}

			/**
			 * Adds data/procedures associated with this class.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully installed, else FALSE.
			 *
			 * @notice Enqueues notice if we do NOT have a readable/writable data directory.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert () === FALSE
			 * @assert (TRUE) === TRUE
			 */
			public function activation_install($confirmation = FALSE)
				{
					$this->check_arg_types('boolean', func_get_args());

					if(!$confirmation)
						return FALSE; // Added security.

					if($this->data_dir_readable_writable())
						return TRUE; // That's it! We're good here.

					$this->©notice->enqueue(
						'<p>'.
						sprintf(
							$this->i18n('Please create this directory: <code>%1$s</code>.'),
							$this->©dirs->get_doc_root_path($this->___instance_config->plugin_data_dir)
						).
						$this->i18n(' You\'ll need to log in via FTP, and set directory permissions to <code>777</code>.').
						$this->i18n(' Please use an application like <a href="http://filezilla-project.org/" target="_blank">FileZilla™</a>.').
						$this->i18n(' See also: <a href="http://www.youtube.com/watch?v=oq0oM2w9lcQ" target="_blank">this video tutorial</a>').
						'</p>'
					);
					return FALSE; // Default return value.
				}

			/**
			 * Removes data/procedures associated with this class.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully uninstalled, else FALSE.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert () === FALSE
			 * @assert (TRUE) === TRUE
			 */
			public function deactivation_uninstall($confirmation = FALSE)
				{
					$this->check_arg_types('boolean', func_get_args());

					if(!$confirmation)
						return FALSE; // Added security.

					$this->empty_and_remove($this->___instance_config->plugin_data_dir);

					return TRUE;
				}
		}
	}