<?php
/**
 * Function Utilities.
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
		 * Function Utilities.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @assert ($GLOBALS[__NAMESPACE__])
		 */
		class functions extends framework
		{
			/**
			 * PHP's language constructs.
			 *
			 * @var array PHP's language constructs.
			 *    Keys are currently unimportant. Subject to change.
			 */
			public $constructs = array(
				'die'             => 'die',
				'echo'            => 'echo',
				'empty'           => 'empty',
				'exit'            => 'exit',
				'eval'            => 'eval',
				'include'         => 'include',
				'include_once'    => 'include_once',
				'isset'           => 'isset',
				'list'            => 'list',
				'require'         => 'require',
				'require_once'    => 'require_once',
				'return'          => 'return',
				'print'           => 'print',
				'unset'           => 'unset',
				'__halt_compiler' => '__halt_compiler'
			);

			/**
			 * Is a particular function, static method, or PHP language construct possible?
			 *
			 * @param string  $function The name of a function, a static method, or a PHP language construct.
			 *
			 * @param string  $reconsider Optional. Empty string default (e.g. do NOT reconsider).
			 *    You MUST use class constant ``\websharks_core_v000000_dev\framework::reconsider`` for this argument value.
			 *    If this is ``\websharks_core_v000000_dev\framework::reconsider``, we force a reconsideration.
			 *
			 * @return boolean TRUE if (in ``$this->constructs`` || ``is_callable()`` || ``function_exists()``),
			 *    and it's NOT been disabled via ``ini_get('disable_functions')`` (or via Suhosin).
			 *    Else this returns FALSE by default.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$function`` is empty.
			 *
			 * @assert ('is_integer') === TRUE
			 * @assert ('is_string') === TRUE
			 * @assert ('eval') === TRUE
			 * @assert ('exit') === TRUE
			 * @assert ('foo') === FALSE
			 */
			public function is_possible($function, $reconsider = '')
				{
					$this->check_arg_types('string:!empty', 'string', func_get_args());

					if(!isset($this->static['is_possible'][$function]) || $reconsider === $this::reconsider)
						{
							$this->static['is_possible'][$function] = FALSE;
							$function                                     = strtolower($function);

							if((in_array($function, $this->constructs, TRUE) || is_callable($function) || function_exists($function))
							   && !in_array($function, $this->disabled(), TRUE) // And it is NOT disabled in some way.
							) $this->static['is_possible'][$function] = TRUE;
						}
					return $this->static['is_possible'][$function];
				}

			/**
			 * Gets all disabled PHP functions.
			 *
			 * @return array An array of all disabled functions, else an empty array.
			 *
			 * @assert () is-type 'array'
			 */
			public function disabled()
				{
					if(!isset($this->static['disabled']))
						{
							$this->static['disabled'] = array();

							if(function_exists('ini_get'))
								{
									if(($_ini_val = trim(strtolower(ini_get('disable_functions')))))
										$this->static['disabled'] = array_merge($this->static['disabled'], preg_split('/[\s;,]+/', $_ini_val, -1, PREG_SPLIT_NO_EMPTY));
									unset($_ini_val); // Housekeeping.

									if(($_ini_val = trim(strtolower(ini_get('suhosin.executor.func.blacklist')))))
										$this->static['disabled'] = array_merge($this->static['disabled'], preg_split('/[\s;,]+/', $_ini_val, -1, PREG_SPLIT_NO_EMPTY));
									unset($_ini_val); // Housekeeping.

									if($this->©string->is_true(ini_get('suhosin.executor.disable_eval')))
										$this->static['disabled'] = array_merge($this->static['disabled'], array('eval'));
								}
						}
					return $this->static['disabled'];
				}

			/**
			 * Array of all backtrace callers (or a specific backtrace caller).
			 *
			 * @param array               $debug_backtrace Array from ``debug_backtrace()``.
			 *
			 * @param null|string|integer $position Defaults to a NULL value (indicating a full array of all callers).
			 *    • Set to `last`, to get the last caller.
			 *    • Set to `previous`, to get the previous caller.
			 *    • Set to `before-previous`, to receive caller, before previous caller.
			 *    • Set to an integer value, to specify an exact array index position.
			 *
			 * @return array|string Array of all backtrace callers. Or, a string with a specific backtrace caller.
			 *    Specific backtrace callers. See ``$offset`` for further details.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert (debug_backtrace(), 'last') === 'websharks_core_v000000_dev\\functionstest->testget_backtrace_callers'
			 */
			public function get_backtrace_callers($debug_backtrace, $position = NULL)
				{
					$this->check_arg_types('array', array('null', 'string', 'integer'), func_get_args());

					$callers = array(); // Initialize array.

					$exclusions = array( // Do not report these in backtrace callers.
						'require*', 'include*',
						'call_user_func*', 'check_*arg_types'
					);

					foreach($debug_backtrace as $_caller) // Compile callers.
						if(is_array($_caller) && $this->©string->is_not_empty($_caller['function'])
						   && !$this->©string->in_wildcard_patterns($_caller['function'], $exclusions)
						) // We exclude a few special functions here.
							{
								if($this->©strings->are_not_empty($_caller['class'], $_caller['type']))
									$callers[] = $_caller['class'].$_caller['type'].$_caller['function'];
								else $callers[] = $_caller['function'];
							}
					unset($_caller); // A little housekeeping.

					if($position === 'last')
						return (!empty($callers[0])) ? strtolower($callers[0]) : 'unknown-caller';
					else if($position === 'previous')
						return (!empty($callers[1])) ? strtolower($callers[1]) : 'unknown-caller';
					else if($position === 'before-previous')
						return (!empty($callers[2])) ? strtolower($callers[2]) : 'unknown-caller';
					else if(is_integer($position))
						return (!empty($callers[$position])) ? strtolower($callers[$position]) : 'unknown-caller';

					return array_map('strtolower', $callers); // Defaults to all callers.
				}
		}
	}