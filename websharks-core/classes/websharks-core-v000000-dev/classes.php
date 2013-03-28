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
			 * @param array $details Defaults to ``array('class_doc_blocks')``.
			 *
			 * @TODO Document/improve array keys. For now, please browse the source code.
			 * @TODO We may also improve/update this routine for codex generation in the future.
			 *
			 * @return array Details about all WebSharks™ Core classes/properties/methods.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 */
			public function get_details($details = array('class_doc_blocks'))
				{
					$this->check_arg_types('array', func_get_args());

					$ns_class_details = array();
					$ns_class         = array('\\'.__NAMESPACE__);

					foreach($this->©dir->iterate(dirname(__FILE__)) as $_dir_file)
						{
							if($_dir_file->isFile()) // We're dealing only with class files here.
								{
									$_file_sub_path          = $this->©dir->n_seps($_dir_file->getSubPathname());
									$_ns_class_file_sub_path = str_replace(array('/', '-'), array('\\', '_'), $_file_sub_path);
									$_sub_path_namespaces    = (string)substr($_ns_class_file_sub_path, 0, strrpos($_ns_class_file_sub_path, '\\'));
									$_ns_class_path          = '\\'.__NAMESPACE__.'\\'.(($_sub_path_namespaces) ? $_sub_path_namespaces.'\\' : '').basename($_ns_class_file_sub_path, '.php');

									if(class_exists($_ns_class_path))
										$ns_class[] = $_ns_class_path;

									else if(class_exists(basename($_ns_class_path)))
										$ns_class[] = basename($_ns_class_path);
								}
						}
					unset($_dir_file, $_file_sub_path, $_ns_class_file_sub_path, $_sub_path_namespaces, $_ns_class_path);

					foreach($ns_class as $_ns_class) // Iterate through all classes.
						{
							$ns_class_details['class: '.$_ns_class]['name'] = $_ns_class;
							$_properties                                    = $_methods = array();
							$_reflection                                    = new \ReflectionClass($_ns_class);

							if(in_array('class_doc_blocks', $details, TRUE))
								$ns_class_details['class: '.$_ns_class]['doc_block'] = "\n\t\t".$_reflection->getDocComment();

							if(in_array('properties', $details, TRUE))
								{
									foreach($_reflection->getProperties() as $_property)
										{
											$_key                       = 'property: $'.$_property->getName();
											$_properties[$_key]['name'] = '$'.$_property->getName();
											if(in_array('property_doc_blocks', $details, TRUE))
												$_properties[$_key]['doc_block'] = "\n\t\t\t\t\t".$_property->getDocComment();
											$_properties[$_key]['modifiers']       = implode(' ', \Reflection::getModifierNames($_property->getModifiers()));
											$_properties[$_key]['declaring-class'] = $_property->getDeclaringClass()->getName();
										}
									$ns_class_details['class: '.$_ns_class]['properties'] = $_properties;
								}
							if(in_array('methods', $details, TRUE))
								{
									foreach($_reflection->getMethods() as $_method)
										{
											$_key                    = 'method: '.$_method->getName().'()';
											$_methods[$_key]['name'] = $_method->getName().'()';
											if(in_array('method_doc_blocks', $details, TRUE))
												$_methods[$_key]['doc_block'] = "\n\t\t\t\t\t".$_method->getDocComment();
											$_methods[$_key]['modifiers']       = implode(' ', \Reflection::getModifierNames($_method->getModifiers()));
											$_methods[$_key]['declaring-class'] = $_method->getDeclaringClass()->getName();

											if(in_array('method_parameters', $details, TRUE))
												foreach($_method->getParameters() as $_parameter)
													if($_parameter->isOptional())
														{
															$__key                                                     = 'param: $'.$_parameter->getName();
															$_methods[$_key]['accepts-parameters'][$__key]['optional'] = TRUE;
															if($_parameter->isPassedByReference())
																$_methods[$_key]['accepts-parameters'][$__key]['only-by-reference'] = TRUE;
															$_methods[$_key]['accepts-parameters'][$__key]['name']          = '$'.$_parameter->getName();
															$_methods[$_key]['accepts-parameters'][$__key]['default-value'] = $_parameter->getDefaultValue();
														}
													else // It's a requirement argument (handle this a bit differently).
														{
															$__key                                                     = 'param: $'.$_parameter->getName();
															$_methods[$_key]['accepts-parameters'][$__key]['required'] = TRUE;
															if($_parameter->isPassedByReference())
																$_methods[$_key]['accepts-parameters'][$__key]['only-by-reference'] = TRUE;
															$_methods[$_key]['accepts-parameters'][$__key]['name'] = '$'.$_parameter->getName();
														}
										}
									$ns_class_details['class: '.$_ns_class]['methods'] = $_methods;
								}
						}
					unset($_reflection, $_properties, $_methods, $_property, $_method, $_key, $__key);

					return $ns_class_details; // This is a HUGE array of all details.
				}
		}
	}