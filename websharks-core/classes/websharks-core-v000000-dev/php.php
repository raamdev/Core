<?php
/**
 * PHP Evaluation.
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
	 * PHP Evaluation.
	 *
	 * @package WebSharks\Core
	 * @since 120318
	 *
	 * @assert ($GLOBALS[__NAMESPACE__])
	 */
	class php extends framework
	{
		/**
		 * Initializer.
		 *
		 * @attaches-to WordPress® `init` action hook.
		 * @hook-priority `2`, when/if PHP enabled by options.
		 *
		 * @return null Nothing.
		 *
		 * @assert () === NULL
		 */
		public function init()
		{
			if(isset($this->static['initialized']))
				return; // Already initialized.

			$this->static['initialized'] = TRUE;

			add_filter('the_content', array($this, 'filter'), 1);
			add_filter('get_the_excerpt', array($this, 'filter'), 1);
			add_filter('widget_text', array($this, 'evaluate'), 1);
		}

		/**
		 * Evaluates PHP conditionally.
		 *
		 * @param string $string Any input string (e.g. content).
		 *
		 * @return string Output string (after possible PHP evaluation).
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 *
		 * @assert ("<?php echo 'hello'; ?>") === "<?php echo 'hello'; ?>"
		 *
		 * @assert $GLOBALS['post'] = (object)array('post_type' => 'page');
		 *    ("<?php echo 'hello'; ?>") === 'hello'
		 */
		public function filter($string)
		{
			$this->check_arg_types('string', func_get_args());

			if($string && isset($GLOBALS['post']->post_type))
			{
				if(in_array($GLOBALS['post']->post_type, $this->©options->get('php.post_types'), TRUE))
					$string = $this->evaluate($string);
			}
			return $string; // Default return value.
		}

		/**
		 * Result of PHP code evaluation.
		 *
		 * @param string $string String (possibly containing PHP tags).
		 *
		 * @return string Output string after having been evaluated by PHP.
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 * @throws exception If unable to evaluate, according to ``can_evaluate()``.
		 *
		 * @assert ("<?php echo 'hello'; ?>") === 'hello'
		 */
		public function evaluate($string)
		{
			$this->check_arg_types('string', func_get_args());

			if($this->©function->is_possible('eval'))
			{
				ob_start();
				eval('?>'.$string.'<?php ');
				return ob_get_clean();
			}
			// Otherwise, let's do a little explaining here.

			throw $this->©exception(
				__METHOD__.'#eval_missing', compact('string'),
				$this->i18n( // Let's do a little explaining here. Why do we NEED ``eval()`` here?
					'The PHP `eval()` function is NOT available on this server (possible security precaution).'.
					' Please check with your hosting provider to resolve this issue and have PHP ``eval()`` enabled.'.
					' Note... the use of ``eval()`` in this plugin, is limited to areas where it is absolutely necessary to achieve a desired functionality.'.
					' For instance, where PHP code is supplied by a site owner (or by their developer) to achieve advanced customization through a UI panel. This can be evaluated at runtime to allow for the inclusion of PHP conditionals or dynamic values.'.
					' In cases such as these, the PHP ``eval()`` function serves a valid purpose. This does NOT introduce a vulnerability, because the code being evaluated has actually been introduced by the site owner (e.g. the code can be trusted in this case).'
				)
			);
		}

		/**
		 * Hilites PHP code, and also shortcodes.
		 *
		 * @param string $string Input string to be hilited.
		 *
		 * @return string The hilited string.
		 *
		 * @throws exception If invalid types are passed through arguments list.
		 *
		 * @assert $string = '[gallery id="123" size="medium"]';
		 *    ($string) === '<code class="hilite"><span style="color: #000000">'."\n".'<span style="color:#946201;">[gallery&nbsp;id="123"&nbsp;size="medium"]</span></span>'."\n".'</code>'
		 *
		 * @assert $string = '[gallery id="123" size="medium"][/gallery]';
		 *    ($string) === '<code class="hilite"><span style="color: #000000">'."\n".'<span style="color:#946201;">[gallery&nbsp;id="123"&nbsp;size="medium"]</span><span style="color:#946201;">[/gallery]</span></span>'."\n".'</code>'
		 */
		public function hilite($string)
		{
			$this->check_arg_types('string', func_get_args());

			$string = highlight_string($string, TRUE);
			$string = $this->©string->replace_once('<code>', '<code class="hilite">', $string);

			return preg_replace_callback('/(?P<shortcode>\[[^\]]+\])/', array($this, '_hilite_shortcodes'), $string);
		}

		/**
		 * Callback handler for hiliting WordPress® Shortcodes.
		 *
		 * @param array $m An array of regex matches.
		 *
		 * @return string Hilited shortcode.
		 *
		 * @throws exception If invalid types are passed through arguments list (disabled).
		 *
		 * @assert (array('[gallery]')) === '<span style="color:#946201;">[gallery]</span>'
		 */
		public function _hilite_shortcodes($m)
		{
			// Commenting this out for performance.
			// This is used as a callback for ``preg_replace()``, so it's NOT absolutely necessary.
			// $this->check_arg_types('array', func_get_args());

			return '<span style="color:#946201;">'.$m[0].'</span>';
		}
	}
}