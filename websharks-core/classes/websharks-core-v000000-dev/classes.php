<?php
/**
 * Classes.
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
		 * Classes.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @assert ($GLOBALS[__NAMESPACE__])
		 */
		class classes extends framework
		{
			/**
			 * Details about all WebSharks™ Core classes/properties/methods.
			 *
			 * @return array Details suitable for ``var_dump()``.
			 */
			public function get_details()
				{
					$core_stub_class  = '\\websharks_core_v000000_dev';
					$ns_class         = array($core_stub_class);
					$ns_class_details = array();

					if(!class_exists($core_stub_class)) // Need this here.
						include_once dirname(dirname(dirname(__FILE__))).'/stub.php';

					foreach($this->©dir->iterate(dirname(__FILE__)) as $_dir_file)
						{
							if($_dir_file->isFile())
								{
									$_sub_path      = $this->©dir->n_seps($_dir_file->getSubPathname());
									$_ns_class_path = str_replace(array('/', '-'), array('\\', '_'), preg_replace('/\.[a-z0-9]+$/i', '', $_sub_path));
									$_sub_namespace = (string)substr($_ns_class_path, 0, strrpos($_ns_class_path, '\\'));
									$_ns_class_path = '\\'.__NAMESPACE__.'\\'.(($_sub_namespace) ? $_sub_namespace.'\\' : '').basename($_ns_class_path);

									if(class_exists($_ns_class_path))
										$ns_class[] = $_ns_class_path;

									else if(class_exists(basename($_ns_class_path)))
										$ns_class[] = basename($_ns_class_path);
								}
						}
					unset($_dir_file, $_sub_path, $_ns_class_path, $_sub_namespace);

					foreach($ns_class as $_ns_class)
						{
							$_reflection = new \ReflectionClass($_ns_class);
							$_doc_block  = "\n\t\t".$_reflection->getDocComment();
							$_properties = $_methods = array();

							foreach($_reflection->getProperties() as $_property)
								{
									$_name = '$'.$_property->getName();

									$_properties[$_name]['name']            = $_name;
									$_properties[$_name]['modifiers']       = implode(' ', \Reflection::getModifierNames($_property->getModifiers()));
									$_properties[$_name]['declaring-class'] = $_property->getDeclaringClass()->getName();
								}
							foreach($_reflection->getMethods() as $_method)
								{
									$_name = $_method->getName().'()';

									$_methods[$_name]['name']            = $_name;
									$_methods[$_name]['modifiers']       = implode(' ', \Reflection::getModifierNames($_method->getModifiers()));
									$_methods[$_name]['declaring-class'] = $_method->getDeclaringClass()->getName();

									foreach($_method->getParameters() as $_parameter)
										{
											$_param_name = '$'.$_parameter->getName();

											if($_parameter->isOptional())
												{
													$_methods[$_name]['accepts-parameters'][$_param_name]['optional'] = TRUE;
													if($_parameter->isPassedByReference())
														$_methods[$_name]['accepts-parameters'][$_param_name]['only-by-reference'] = TRUE;
													$_methods[$_name]['accepts-parameters'][$_param_name]['name']          = $_param_name;
													$_methods[$_name]['accepts-parameters'][$_param_name]['default-value'] = $_parameter->getDefaultValue();
												}
											else // It's a requirement argument (handle this a bit differently).
												{
													$_methods[$_name]['accepts-parameters'][$_param_name]['required'] = TRUE;
													if($_parameter->isPassedByReference())
														$_methods[$_name]['accepts-parameters'][$_param_name]['only-by-reference'] = TRUE;
													$_methods[$_name]['accepts-parameters'][$_param_name]['name'] = $_param_name;
												}
										}
								}
							$ns_class_details[$_ns_class] = array('doc_block' => $_doc_block, 'properties' => $_properties, 'methods' => $_methods);
						}
					unset($_reflection, $_doc_block, $_properties, $_property, $_methods, $_method, $_name);

					return $ns_class_details;
				}
		}
	}